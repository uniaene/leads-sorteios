import express from "express";
import pool from "../db.js";
import redis, { LEADS_QUEUE } from "../redis.js";

const router = express.Router();

// validação simples
function sanitize(s) {
    return String(s || "").trim();
}

// POST /leads  → empilha na fila (Redis) e responde rápido
router.post("/", async (req, res) => {
    try {
        const local_id = Number(req.body.local_id || req.body.local);
        const fullname = sanitize(req.body.fullname);
        const email = sanitize(req.body.email).toLowerCase();
        const whatsapp = sanitize(req.body.whatsapp);
        const course = sanitize(req.body.course);
        const terms = String(req.body.terms).toLowerCase() === "yes" ? 1 : 0;

        if (!local_id || !fullname || !email || !whatsapp || !course || !terms) {
            return res.status(400).json({ success: false, message: "Campos obrigatórios faltando." });
        }

        // anti-bot simples: honeypot
        if (req.body.website && String(req.body.website).length > 0) {
            return res.json({ success: true, message: "Recebido!" });
        }

        // Checagem leve de local_id
        // (poderia cachear locals em memória/redis e validar sem ir ao DB)
        // aqui é opcional: comentar para ainda mais velocidade
        {
            const { rows } = await pool.query("SELECT 1 FROM locals WHERE id=$1 AND status=1", [local_id]);
            if (!rows.length) {
                return res.status(400).json({ success: false, message: "Local inválido ou inativo." });
            }
        }

        // joga na fila
        const payload = JSON.stringify({ local_id, fullname, email, whatsapp, course, terms });
        await redis.rpush(LEADS_QUEUE, payload);

        return res.json({ success: true, message: "Cadastro recebido!" });
    } catch (e) {
        console.error(e);
        return res.status(500).json({ success: false, message: "Erro interno" });
    }
});

// (opcional) endpoint direto (sem fila) para contingência
router.post("/direct", async (req, res) => {
    try {
        const local_id = Number(req.body.local_id || req.body.local);
        const fullname = sanitize(req.body.fullname);
        const email = sanitize(req.body.email).toLowerCase();
        const whatsapp = sanitize(req.body.whatsapp);
        const course = sanitize(req.body.course);
        const terms = String(req.body.terms).toLowerCase() === "yes" ? 1 : 0;

        if (!local_id || !fullname || !email || !whatsapp || !course || !terms) {
            return res.status(400).json({ success: false, message: "Campos obrigatórios faltando." });
        }

        const q = `
      INSERT INTO registros_visitas (local_id, fullname, email, whatsapp, course, terms, created_at)
      VALUES ($1,$2,$3,$4,$5,$6,NOW())
      ON CONFLICT (local_id, lower(fullname), lower(email)) DO NOTHING
      RETURNING id
    `;
        const values = [local_id, fullname, email, whatsapp, course, terms];
        const { rowCount } = await pool.query(q, values);

        if (rowCount > 0) {
            return res.json({ success: true, message: "Cadastro efetuado!" });
        } else {
            return res.json({ success: false, message: "Você já se cadastrou." });
        }
    } catch (e) {
        console.error(e);
        return res.status(500).json({ success: false, message: "Erro interno" });
    }
});

export default router;
