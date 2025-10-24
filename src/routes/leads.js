import express from "express";
import pool from "../db.js";
import redis, { LEADS_QUEUE } from "../redis.js";

const router = express.Router();

router.post("/", async (req, res) => {
    console.log("📩 Recebido:", req.body);

    try {
        let { local_id, fullname, email, whatsapp, course, terms } = req.body;

        // 🔧 Normaliza os tipos
        local_id = Number(local_id);
        terms = (terms === true || terms === "true" || terms === 1 || terms === "1") ? 1 : 0;

        // 🧪 Validação detalhada
        if (
            !local_id ||
            typeof fullname !== "string" || fullname.trim() === "" ||
            typeof email !== "string" || email.trim() === "" ||
            typeof whatsapp !== "string" || whatsapp.trim() === "" ||
            typeof course !== "string" || course.trim() === "" ||
            terms !== 1
        ) {
            console.warn("⚠️ Falha na validação:", { local_id, fullname, email, whatsapp, course, terms });
            return res.status(400).json({
                success: false,
                message: "Campos obrigatórios faltando ou inválidos.",
            });
        }

        // 🧭 Confirma se o local existe
        console.log("🔍 Verificando local_id:", local_id);
        const localCheck = await pool.query("SELECT id FROM locals WHERE id = $1", [local_id]);

        if (localCheck.rows.length === 0) {
            console.warn("⚠️ Local inválido:", local_id);
            return res.status(400).json({
                success: false,
                message: "Local não encontrado.",
            });
        }

        // 🧱 Monta o objeto e envia para o Redis
        const leadData = JSON.stringify({
            local_id,
            fullname,
            email,
            whatsapp,
            course,
            terms,
        });

        console.log("📦 Enfileirando lead:", email);
        await new Promise((resolve, reject) => {
            redis.rpush(LEADS_QUEUE, leadData, (err) => {
                if (err) return reject(err);
                resolve();
            });
        });


        console.log("✅ Lead enfileirado com sucesso:", email);
        return res.json({
            success: true,
            message: "Cadastro recebido e enfileirado com sucesso!",
        });
    } catch (err) {
        console.error("💥 Erro no POST /leads:", err);

        // 🧩 Identifica a origem do erro
        let origem = "interno";
        if (err.message.includes("redis")) origem = "Redis";
        if (err.message.includes("pool") || err.message.includes("relation")) origem = "PostgreSQL";

        return res.status(500).json({
            success: false,
            message: `Erro interno (${origem}).`,
            details: err.message,
        });
    }
});

export default router;
