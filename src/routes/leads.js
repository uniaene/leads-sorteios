import express from "express";
import pool from "../db.js";
import redis, { LEADS_QUEUE } from "../redis.js";

const router = express.Router();

router.post("/", async (req, res) => {
    try {
        console.log("📩 Recebido:", req.body);

        let { local_id, fullname, email, whatsapp, course, terms } = req.body;

        // Normaliza tipos
        local_id = Number(local_id);
        terms = (terms === true || terms === "true" || terms === 1 || terms === "1") ? 1 : 0;

        // Validação segura
        if (
            !local_id ||
            typeof fullname !== "string" ||
            typeof email !== "string" ||
            typeof whatsapp !== "string" ||
            typeof course !== "string" ||
            terms !== 1
        ) {
            console.log("❌ Falha na validação:", { local_id, fullname, email, whatsapp, course, terms });
            return res.status(400).json({ success: false, message: "Campos obrigatórios faltando." });
        }

        // Verifica se o local existe
        const localCheck = await pool.query("SELECT id FROM locals WHERE id = $1", [local_id]);
        if (localCheck.rows.length === 0) {
            return res.status(400).json({ success: false, message: "Local inválido." });
        }

        // Empilha no Redis
        const leadData = JSON.stringify({ local_id, fullname, email, whatsapp, course, terms });
        await redis.rPush(LEADS_QUEUE, leadData);

        console.log("✅ Lead enfileirado:", email);
        res.json({ success: true, message: "Cadastro recebido!" });
    } catch (err) {
        console.error("💥 Erro no POST /leads:", err.message);
        res.status(500).json({ success: false, message: "Erro interno do servidor." });
    }
});

export default router;
