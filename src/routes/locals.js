import express from "express";
import pool from "../db.js";
import redis from "../redis.js";

const router = express.Router();

// GET /locals  (cache 2s no Redis)
router.get("/", async (req, res) => {
    try {
        const cacheKey = "locals_cache_v1";
        const hit = await redis.get(cacheKey);
        if (hit) {
            return res.json(JSON.parse(hit));
        }

        const { rows } = await pool.query(
            "SELECT id, local FROM locals WHERE status = 1 ORDER BY local ASC"
        );

        await redis.setex(cacheKey, 2, JSON.stringify(rows));
        return res.json(rows);
    } catch (e) {
        console.error(e);
        return res.status(500).json({ error: "Erro ao listar locais" });
    }
});

export default router;
