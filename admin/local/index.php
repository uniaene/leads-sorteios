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
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Obtendo os valores do formulário
        $local = isset($_POST['local']) ? $_POST['local'] : null;

        // Verificação básica de campos obrigatórios
        if ($local) {

            // Inserção dos dados na tabela
            $sql = "INSERT INTO locals (local, status) VALUES (:local, 1)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':local', $local);

            // Executa a query
            if ($stmt->execute()) {
                $success = $conn->lastInsertId();
            }
        }
    }
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

// Fechar a conexão
$conn = null;

//Aqui vai um retorno em json
$json["success"] = $success;
$json["message"] = $message;
echo json_encode($json);
return;
