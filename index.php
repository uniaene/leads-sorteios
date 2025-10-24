<?php

/**
 * UNIAENE - Sistema de Captação de Leads / Sorteio
 * Otimizado com Redis + PDO Persistente
 */

$servername = "localhost";
$username   = "adventis_visita";
$password   = "UM2u+k]i&d(B";
$dbname     = "adventis_visita";

try {
    // 🔹 Conexão Redis
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $cacheKey = 'locals_cache';
    $locals_json = $redis->get($cacheKey);

    if ($locals_json) {
        // Cache HIT
        $locals = json_decode($locals_json);
    } else {
        // Cache MISS → busca no MySQL
        $conn = new PDO(
            "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true,
            ]
        );

        $stmt = $conn->query("SELECT id, local FROM locals WHERE status = 1");
        $locals = $stmt->fetchAll(PDO::FETCH_OBJ);

        $redis->setex($cacheKey, 300, json_encode($locals)); // cache 5 minutos
    }
} catch (Exception $e) {
    error_log("Erro Redis/MySQL: " . $e->getMessage());
    die("<h2>Ocorreu um erro temporário. Tente novamente em instantes.</h2>");
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UNIAENE - Centro Universitário Adventista de Ensino do Nordeste</title>
    <link rel="icon" href="img/favicon/favicon-32x32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid full-height">
        <div class="row row-height">
            <div class="col-lg-6 content-left image-left">
                <div class="content-left-wrapper">
                    <img src="assets/images/logo-azul.png" alt="UNIAENE" height="70" class="mb-4">
                    <h1 class="fw-bold">UNIAENE</h1>
                    <p>Faça parte dessa história</p>
                    <a href="https://www.adventista.edu.br/" target="_blank" class="btn btn-warning mb-2">Visite nosso site</a>
                </div>
            </div>

            <div class="col-lg-6 content-right" id="start">
                <div id="wizard_container" style="width: 600px;">
                    <form id="wrapped" method="POST" action="registration/index.php">
                        <div class="step">
                            <h3><strong>1/3</strong> Preencha com os seus dados</h3>
                            <div class="form-group">
                                <select class="form-select" name="local" required>
                                    <option value="">Local</option>
                                    <?php foreach ($locals as $local): ?>
                                        <option value="<?= $local->id ?>"><?= htmlspecialchars($local->local) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <input type="text" name="fullname" class="form-control mb-2" placeholder="Nome completo" required>
                            <input type="email" name="email" class="form-control mb-2" placeholder="E-mail" required>
                            <input type="text" name="whatsapp" class="form-control mb-2" placeholder="WhatsApp" required>
                            <label><input type="checkbox" name="terms" value="Yes" required> Aceito compartilhar meus dados</label>
                        </div>

                        <div class="step">
                            <h3><strong>2/3</strong> Curso de interesse</h3>
                            <select class="form-select" name="course" required>
                                <option value="">Selecione...</option>
                                <option value="Administração">Administração</option>
                                <option value="Direito">Direito</option>
                                <option value="Enfermagem">Enfermagem</option>
                                <option value="Fisioterapia">Fisioterapia</option>
                                <option value="Teologia">Teologia</option>
                                <option value="Psicologia">Psicologia</option>
                                <option value="Odontologia">Odontologia</option>
                                <option value="Pedagogia">Pedagogia</option>
                                <option value="Nutrição">Nutrição</option>
                            </select>
                        </div>

                        <div class="step text-center">
                            <h3><strong>3/3</strong> Enviar</h3>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.post($(this).attr('action'), $(this).serialize(), function(r) {
                let res = JSON.parse(r);
                alert(res.message);
            });
        });
    </script>
</body>

</html>