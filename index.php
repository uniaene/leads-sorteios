<?php
/**
 * UNIAENE - Sistema de Captação de Leads / Sorteio
 * Arquivo otimizado para alto volume de acessos simultâneos
 * 
 * Melhorias:
 * - Conexão PDO persistente
 * - Cache simples de 60s para lista de locais
 * - Filtro no SQL (status=1)
 * - Tratamento de erros seguro
 * - HTML e scripts otimizados
 */

$servername = "localhost";
$username   = "adventis_visita";
$password   = "UM2u+k]i&d(B";
$dbname     = "adventis_visita";

$cacheFile = __DIR__ . '/cache/locals.json';
$cacheTTL  = 60; // segundos

try {
    // Usa cache se for recente
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTTL)) {
        $locals = json_decode(file_get_contents($cacheFile));
    } else {
        $conn = new PDO(
            "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true,
            ]
        );

        $sql = "SELECT id, local FROM locals WHERE status = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $locals = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (!is_dir(__DIR__ . '/cache')) {
            mkdir(__DIR__ . '/cache', 0755, true);
        }
        file_put_contents($cacheFile, json_encode($locals));
    }
} catch (PDOException $e) {
    error_log("Erro MySQL: " . $e->getMessage());
    die("<h2>Ocorreu um erro temporário. Tente novamente em instantes.</h2>");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="UNIAENE - Faça parte dessa história">
    <meta name="author" content="UNIAENE">
    <title>UNIAENE - Centro Universitário Adventista de Ensino do Nordeste</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,500,600" rel="stylesheet">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/menu.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/vendors.css" rel="stylesheet">

    <style>
        .radio-inline { display: flex; align-items: center; }
        .radio-inline input { margin-right: 10px; }
    </style>
</head>

<body>

    <div id="preloader"><div data-loader="circle-side"></div></div>
    <div id="loader_form"><div data-loader="circle-side-2"></div></div>

    <nav>
        <ul class="cd-primary-nav">
            <li><a href="https://www.adventista.edu.br/" target="_blank" class="animated_link">Site Oficial</a></li>
            <li><a href="https://www.instagram.com/uniaene" target="_blank" class="animated_link">Instagram</a></li>
            <li><a href="https://www.youtube.com/channel/UC6GjhC0KktsGmiPtJ92mVdQ" target="_blank" class="animated_link">YouTube</a></li>
            <li><a href="https://www.facebook.com/FaculdadeAdventista" target="_blank" class="animated_link">Facebook</a></li>
            <li><a href="https://twitter.com/fac_adventista" target="_blank" class="animated_link">X (Twitter)</a></li>
        </ul>
    </nav>

    <div class="container-fluid full-height">
        <div class="row row-height">
            <div class="col-lg-6 content-left image-left">
                <div class="content-left-wrapper">
                    <a href="./" id="logo" class="d-none d-sm-block">
                        <img src="assets/images/logo-azul.png" alt="UNIAENE" height="70">
                    </a>
                    <a href="./" id="logo" class="d-block d-sm-none">
                        <img src="assets/images/logo-azul.png" alt="UNIAENE" height="35">
                    </a>

                    <div id="social">
                        <ul>
                            <li><a href="https://www.instagram.com/uniaene" target="_blank"><i class="bi bi-instagram text-dark"></i></a></li>
                            <li><a href="https://www.youtube.com/channel/UC6GjhC0KktsGmiPtJ92mVdQ" target="_blank"><i class="bi bi-youtube text-dark"></i></a></li>
                            <li><a href="https://www.facebook.com/FaculdadeAdventista" target="_blank"><i class="bi bi-facebook text-dark"></i></a></li>
                        </ul>
                    </div>

                    <div>
                        <h1 class="fw-bold">UNIAENE</h1>
                        <p>Faça parte dessa história</p>
                        <a href="https://www.adventista.edu.br/" class="btn_1 rounded mb-2" style="background: yellow; color: black;" target="_blank"><i class="bi bi-link"></i> Visite nosso site</a>
                        <a href="https://www.instagram.com/uniaene" class="btn_1 rounded mb-2" style="background: yellow; color: black;" target="_blank"><i class="bi bi-instagram"></i> Siga-nos no Instagram</a>
                        <a href="https://www.youtube.com/watch?v=RIshmy59gC4" class="btn_1 rounded mb-2" style="background: cyan; color: black;" target="_blank"><i class="bi bi-youtube text-danger"></i> Assista ao nosso clipe</a>
                    </div>

                    <div class="copy">©<?= date("Y") ?> UNIAENE</div>
                </div>
            </div>

            <div class="col-lg-6 content-right" id="start">
                <div id="wizard_container" style="width: 600px;">
                    <div id="top-wizard"><div id="progressbar"></div></div>

                    <form id="wrapped" method="POST">
                        <input id="website" name="website" type="text" value="">
                        <div id="middle-wizard" style="min-height: inherit;">
                            <div class="step">
                                <h3 class="main_question"><strong>1/3</strong> Preencha com os seus dados</h3>
                                <div class="form-group">
                                    <div class="styled-select clearfix">
                                        <select class="wide required" name="local" onchange="getVals(this, 'local');">
                                            <option value="">Local</option>
                                            <?php foreach ($locals as $local): ?>
                                                <option value="<?= $local->id ?>"><?= htmlspecialchars($local->local) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group"><input type="text" name="fullname" class="form-control required" placeholder="Digite seu nome completo" onchange="getVals(this, 'fullname');"></div>
                                <div class="form-group"><input type="email" name="email" class="form-control required" placeholder="Digite seu E-mail" onchange="getVals(this, 'email');"></div>
                                <div class="form-group">
                                    <input type="text" name="whatsapp" class="form-control required phone" placeholder="Digite seu WhatsApp no formato (00) 0000-0000" onchange="getVals(this, 'whatsapp');">
                                    <span class="text-muted small">Pode ser o WhatsApp dos responsáveis</span>
                                </div>
                                <div class="form-group terms">
                                    <label class="container_check">Aceito compartilhar meus dados
                                        <input type="checkbox" name="terms" value="Yes" class="required">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="step">
                                <h3 class="main_question"><strong>2/3</strong> Cursos de interesse</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Administração"> Administração</label><br>
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Direito"> Direito</label><br>
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Enfermagem"> Enfermagem</label><br>
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Fisioterapia"> Fisioterapia</label><br>
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Teologia"> Teologia</label><br>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Psicologia"> Psicologia</label><br>
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Odontologia"> Odontologia</label><br>
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Pedagogia"> Pedagogia</label><br>
                                        <label class="radio-inline"><input class="green" type="radio" name="course" value="Nutrição"> Nutrição</label><br>
                                    </div>
                                </div>
                            </div>

                            <div class="submit step">
                                <h3 class="main_question"><strong>3/3</strong> Confira os dados</h3>
                                <div class="summary">
                                    <ul>
                                        <li><label>Nome</label>: <span id="fullname"></span></li>
                                        <li><label>E-mail</label>: <span id="email"></span></li>
                                        <li><label>WhatsApp</label>: <span id="whatsapp"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div id="bottom-wizard">
                            <button type="button" name="backward" class="backward">Voltar</button>
                            <button type="button" name="forward" class="forward">Próximo</button>
                            <button type="submit" name="process" class="submit">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts no final e com defer -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="js/common_scripts.min.js" defer></script>
    <script src="js/functions.js" defer></script>
    <script src="js/registration_func.js" defer></script>

    <script defer>
        document.addEventListener("DOMContentLoaded", function() {
            var SPMaskBehavior = function(val) {
                    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                },
                spOptions = {
                    onKeyPress: function(val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
            $('.phone').mask(SPMaskBehavior, spOptions);
        });
    </script>
</body>
</html>
