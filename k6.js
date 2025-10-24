import http from "k6/http";
import { sleep, check } from "k6";

export let options = {
    scenarios: {
        spike: {
            executor: "constant-vus",
            vus: 4000,
            duration: "30s"
        }
    },
    thresholds: {
        http_req_failed: ["rate<0.01"],
        http_req_duration: ["p(95)<800"]
    }
};

const API = "http://52.23.233.158/";

export default function () {
    // 1) pega locais (cacheados em Redis)
    let r1 = http.get(`${API}/locals`);
    check(r1, { "locals 200": (res) => res.status === 200 });

    // 2) envia lead para fila
    const locals = r1.json();
    const local_id = locals.length ? locals[Math.floor(Math.random() * locals.length)].id : 1;

    const payload = {
        local_id,
        fullname: `Teste ${Math.random().toString(36).slice(2)}`,
        email: `t${Math.floor(Math.random() * 1e9)}@exemplo.com`,
        whatsapp: "(11) 99999-9999",
        course: "Administração",
        terms: "Yes",
        website: "" // honeypot
    };

    let r2 = http.post(`${API}/leads`, payload);
    check(r2, { "leads 200": (res) => res.status === 200 });
    sleep(0.2);
}
