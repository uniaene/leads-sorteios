<?php

/**
 * UNIAENE - Registro de Leads (com suporte a Redis Queue)
 */

$servername = "localhost";
$username   = "adventis_visita";
$password   = "UM2u+k]i&d(B";
$dbname     = "adventis_visita";

$success = false;
$message = "Erro desconhecido.";
$useQueue = true; // ativa fila Redis

try {
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
        $data = [
            "local" => trim($_POST['local'] ?? ''),
            "fullname" => trim($_POST['fullname'] ?? ''),
            "email" => trim($_POST['email'] ?? ''),
            "whatsapp" => trim($_POST['whatsapp'] ?? ''),
            "course" => trim($_POST['course'] ?? ''),
            "terms" => ($_POST['terms'] ?? '') === 'Yes' ? 1 : 0
        ];

        if (in_array('', [$data['local'], $data['fullname'], $data['email'], $data['whatsapp'], $data['course']]) || !$data['terms']) {
            echo json_encode(["success" => false, "message" => "Preencha todos os campos."]);
            exit;
        }

        // 🔹 Redis Queue (para absorver picos)
        if ($useQueue && class_exists('Redis')) {
            try {
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379);
                $redis->rPush('leads_queue', json_encode($data));
                echo json_encode(["success" => true, "message" => "Cadastro recebido! (em fila)"]);
                exit;
            } catch (Exception $e) {
                $useQueue = false;
            }
        }

        // 🔹 Inserção direta (fallback)
        $stmt = $conn->prepare("
            INSERT IGNORE INTO registros_visitas 
            (local, fullname, email, whatsapp, course, terms, created_at)
            VALUES (:local, :fullname, :email, :whatsapp, :course, :terms, NOW())
        ");
        $stmt->execute($data);

        if ($stmt->rowCount() > 0) {
            $success = true;
            $message = "Cadastro efetuado com sucesso!";
        } else {
            $message = "Você já se cadastrou.";
        }
    }
} catch (PDOException $e) {
    $message = "Erro de banco: " . $e->getMessage();
} catch (Throwable $e) {
    $message = "Erro inesperado: " . $e->getMessage();
}

echo json_encode(["success" => $success, "message" => $message]);
