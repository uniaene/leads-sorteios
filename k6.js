import http from "k6/http";
import { sleep } from "k6";

export let options = {
  vus: 500,          // 500 usuários simultâneos
  duration: "5s",   // duração do teste
};

export default function () {
  const url = "https://sorteio.uniaene.edu.br/leads";
  const payload = JSON.stringify({
    local_id: 150, // ajuste conforme SELECT acima
    fullname: "Teste " + Math.random().toString(36).substring(2),
    email: `teste${Math.floor(Math.random()*100000)}@exemplo.com`,
    whatsapp: "(11) 99999-9999",
    course: "Administração",
    terms: 1
  });
  const headers = { "Content-Type": "application/json" };
  http.post(url, payload, { headers });
  sleep(0.2);
}