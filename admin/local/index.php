<?php
$servername = "localhost";
$username   = "adventis_visita";
$password   = "UM2u+k]i&d(B";
$dbname     = "adventis_visita";

$success = false;
$message = "Houve algum erro.";

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['local'])) {
        $stmt = $conn->prepare("INSERT INTO locals (local, status) VALUES (:local, 1)");
        $stmt->execute([':local' => trim($_POST['local'])]);
        $success = $conn->lastInsertId();

        // 🔹 Limpa cache Redis
        try {
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->del('locals_cache');
        } catch (Exception $e) {
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}

echo json_encode(["success" => $success, "message" => $message]);
