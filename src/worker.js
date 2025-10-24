import dotenv from "dotenv";
dotenv.config();

import pool from "./db.js";
import redis, { LEADS_QUEUE } from "./redis.js";

async function run() {
    console.log("Worker iniciado. Consumindo fila:", LEADS_QUEUE);

    while (true) {
        try {
            const res = await redis.blpop(LEADS_QUEUE, 5); // timeout 5s
            if (!res) continue;

            const [, payload] = res;
            const data = JSON.parse(payload);

            const localId = parseInt(data.local) || parseInt(data.local_id) || null;

            if (!localId) {
                console.error("⚠️ local_id ausente ou inválido:", data);
                continue;
            }

            const q = `
                INSERT INTO registros_visitas 
                    (local_id, fullname, email, whatsapp, course, terms, created_at)
                VALUES ($1,$2,$3,$4,$5,$6,NOW())
                ON CONFLICT (local_id, lower(fullname), lower(email)) DO NOTHING
                RETURNING id
            `;

            const values = [
                localId,
                data.fullname?.trim() || "",
                data.email?.trim() || "",
                data.whatsapp?.trim() || "",
                data.course?.trim() || "",
                data.terms === "Yes" || data.terms === true ? 1 : 0 // 🧩 fix aqui
            ];

            const result = await pool.query(q, values);

            if (result.rowCount > 0) {
                console.log(`✅ Novo lead inserido: ${data.fullname} (${data.email})`);
            } else {
                console.log(`⚠️ Lead duplicado ignorado: ${data.fullname}`);
            }

        } catch (e) {
            console.error("❌ Erro no worker:", e.message);
            await new Promise(r => setTimeout(r, 500));
        }
    }
}

run();
