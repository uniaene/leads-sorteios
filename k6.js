import http from "k6/http";
import { sleep } from "k6";

// Configurações do teste
export let options = {
  vus: 4000,         // número de usuários virtuais simultâneos
  duration: "30s",   // tempo total do teste
  thresholds: {
    http_req_failed: ["rate<0.01"],   // menos de 1% de falhas aceitável
    http_req_duration: ["p(95)<800"], // 95% das requisições abaixo de 800ms
  },
};

export default function () {
  const url = "https://sorteio.uniaene.edu.br/leads"; // use HTTPS e domínio
  const payload = {
    local_id: 150,
    fullname: "Teste " + Math.random().toString(36).substring(2),
    email: `teste${Math.floor(Math.random()*100000)}@exemplo.com`,
    whatsapp: "(11) 99999-9999",
    course: "Administração",
    terms: true
  };

  const headers = { "Content-Type": "application/json" };
  http.post(url, JSON.stringify(payload), { headers });

  sleep(0.3);
}