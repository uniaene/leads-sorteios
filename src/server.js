import express from "express";
import cors from "cors";
import helmet from "helmet";
import compression from "compression";
import morgan from "morgan";
import rateLimit from "express-rate-limit";
import dotenv from "dotenv";
import localsRouter from "./routes/locals.js";
import leadsRouter from "./routes/leads.js";

dotenv.config();

const app = express();
const PORT = Number(process.env.PORT || 3000);

// middlewares
app.use(helmet());
app.use(compression());
app.use(express.json({ limit: "200kb" }));
app.use(express.urlencoded({ extended: true, limit: "200kb" }));
app.use(morgan("combined"));
app.use(cors({
    origin: process.env.CORS_ORIGIN?.split(",") || "*"
}));

// rate limit (anti-abuso por IP)
const isStress = process.env.STRESS_TEST === "true";

if (!isStress) {
    const limiter = rateLimit({
        windowMs: 5 * 1000, // 5s
        max: 40,            // 40 req/5s por IP (~8 rps)
        standardHeaders: true,
        legacyHeaders: false
    });
    app.use(limiter);
} else {
    console.log("⚡ Rate limiter desativado (modo stress test)");
}

// rotas
app.get("/", (_, res) => res.json({ token: "loaderio-92742a39271319ea20a7897b350b2671" }));
app.get("/loaderio-92742a39271319ea20a7897b350b2671", (_, res) => res.json({ token: "loaderio-92742a39271319ea20a7897b350b2671" }));
app.get("/health", (_, res) => res.json({ ok: true }));
app.use("/locals", localsRouter);
app.use("/leads", leadsRouter);

// 404
app.use((req, res) => res.status(404).json({ error: "Not found" }));

const HOST = "0.0.0.0"; // <- escuta em todas as interfaces

app.listen(PORT, HOST, () => {
    console.log(`✅ API rodando em http://${HOST}:${PORT}`);
});
