import dotenv from "dotenv";
dotenv.config();

import pool from "./db.js";
import redis, { LEADS_QUEUE } from "./redis.js";

// Consome a fila LEADS_QUEUE com BLPOP (bloqueante)
async function run() {
    console.log("Worker iniciado. Consumindo fila:", LEADS_QUEUE);

    while (true) {
        try {
            const res = await redis.blpop(LEADS_QUEUE, 5); // 5s timeout
            if (!res) continue;

            const [, payload] = res; // [queue, value]
            const data = JSON.parse(payload);

            const q = `
        INSERT INTO registros_visitas (local_id, fullname, email, whatsapp, course, terms, created_at)
        VALUES ($1,$2,$3,$4,$5,$6,NOW())
        ON CONFLICT (local_id, lower(fullname), lower(email)) DO NOTHING
        RETURNING id
      `;
            const values = [
                data.local_id, data.fullname, data.email,
                data.whatsapp, data.course, data.terms
            ];
            await pool.query(q, values);
        } catch (e) {
            console.error("Erro no worker:", e.message);
            await new Promise(r => setTimeout(r, 500));
        }
    }
}

run();
