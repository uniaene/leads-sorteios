import Redis from "ioredis";
import dotenv from "dotenv";
dotenv.config();

const redis = new Redis({
    host: process.env.REDIS_HOST || "127.0.0.1",
    port: Number(process.env.REDIS_PORT) || 6379,
    // password: process.env.REDIS_PASSWORD,
    maxRetriesPerRequest: 2,
    enableReadyCheck: true
});

// chave da fila
export const LEADS_QUEUE = "leads_queue";

export default redis;
