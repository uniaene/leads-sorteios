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

    if (!empty($_GET['local'])) {
        $id = (int) $_GET['local'];
        $stmt = $conn->prepare("SELECT status FROM locals WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $local = $stmt->fetch(PDO::FETCH_OBJ);

        if ($local) {
            $newStatus = $local->status ? 0 : 1;
            $stmt = $conn->prepare("UPDATE locals SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $newStatus, ':id' => $id]);
            $success = true;

            // 🔹 Limpa cache Redis
            $redis = new Redis();
            $redis->connect('127.0.0.1', 6379);
            $redis->del('locals_cache');
        }
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
}

echo json_encode(["success" => $success, "message" => $message]);
