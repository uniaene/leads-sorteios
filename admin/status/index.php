<?php
$servername = "localhost";
$username = "adventis_visita";
$password = "UM2u+k]i&d(B";
$dbname = "adventis_visita";

$success = false;
$message = "Houve algum erro";

try {
    // Conexão com o banco de dados
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Definindo o modo de erro para exceções
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se o formulário foi submetido
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {

        //Conferindo o status atual
        $sql = "SELECT * FROM locals WHERE id = :local";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':local', $local, PDO::PARAM_INT);
        $stmt->execute();
        $local = $stmt->fetch(PDO::FETCH_OBJ);

        if ($local) {

            // Inserção dos dados na tabela
            $sql = "INSERT INTO locals (local, status) VALUES (:local, 1)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':local', $local->status == 1 ? 0 : 1);

            // Executa a query
            if ($stmt->execute()) {
                $success = true;
            }
        }
    }
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

// Fechar a conexão
$conn = null;

//Aqui vai um retorno em json
if ($success) {
    $json["reload"] = true;
    echo json_encode($json);
    return;
}

$json["success"] = $success;
$json["message"] = $message;
echo json_encode($json);
