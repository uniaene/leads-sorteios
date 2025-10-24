<?php
/**
 * UNIAENE - Sistema de Captação de Leads Otimizado
 * by Fernando Issler + GPT-5
 */

$servername = "localhost";
$username   = "adventis_visita";
$password   = "UM2u+k]i&d(B";
$dbname     = "adventis_visita";

$success = false;
$message = "Erro desconhecido.";
$useQueue = false; // mudar para true se quiser ativar fila com Redis

try {
    // Conexão PDO com persistência (melhor performance sob carga)
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true
        ]
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Sanitização simples
        $local     = trim($_POST['local'] ?? '');
        $fullname  = trim($_POST['fullname'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $whatsapp  = trim($_POST['whatsapp'] ?? '');
        $course    = trim($_POST['course'] ?? '');
        $terms     = isset($_POST['terms']) && $_POST['terms'] === 'Yes' ? 1 : 0;

        // Validação
        if (empty($local) || empty($fullname) || empty($email) || empty($whatsapp) || empty($course) || !$terms) {
            echo json_encode(["success" => false, "message" => "Preencha todos os campos obrigatórios."]);
            exit;
        }

        // Se quiser usar Redis para fila
        if ($useQueue && class_exists('Redis')) {
            try {
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);
                $leadData = json_encode([
                    "local" => $local,
                    "fullname" => $fullname,
                    "email" => $email,
                    "whatsapp" => $whatsapp,
                    "course" => $course,
                    "terms" => $terms
                ]);
                $redis->rPush('leads_queue', $leadData);
                $success = true;
                $message = "Cadastro recebido! Ele será processado em alguns instantes.";
            } catch (Exception $e) {
                // fallback se Redis estiver indisponível
                $useQueue = false;
            }
        }

        // Caso não use Redis ou o fallback seja necessário
        if (!$useQueue) {
            // Usa INSERT IGNORE + índice único (evita duplicados automaticamente)
            $sql = "INSERT IGNORE INTO registros_visitas (local, fullname, email, whatsapp, course, terms, created_at)
                    VALUES (:local, :fullname, :email, :whatsapp, :course, :terms, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':local'     => $local,
                ':fullname'  => $fullname,
                ':email'     => $email,
                ':whatsapp'  => $whatsapp,
                ':course'    => $course,
                ':terms'     => $terms
            ]);

            if ($stmt->rowCount() > 0) {
                $success = true;
                $message = "Cadastro efetuado com sucesso!";
            } else {
                $message = "Você já se cadastrou.";
            }
        }
    }
} catch (PDOException $e) {
    $message = "Erro de banco de dados: " . $e->getMessage();
} catch (Throwable $e) {
    $message = "Erro inesperado: " . $e->getMessage();
}

echo json_encode([
    "success" => $success,
    "message" => $message
]);
