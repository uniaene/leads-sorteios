import { createClient } from "redis";

export const LEADS_QUEUE = "leads_queue";

const redis = createClient({
    socket: {
        host: process.env.REDIS_HOST || "127.0.0.1",
        port: process.env.REDIS_PORT || 6379,
    },
    password: process.env.REDIS_PASSWORD || undefined,
});

redis.on("error", (err) => console.error("❌ Erro no Redis:", err.message));
await redis.connect();
console.log("✅ Conectado ao Redis com sucesso");

export default redis;
