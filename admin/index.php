<?php

$servername = "localhost";
$username = "adventis_visita";
$password = "UM2u+k]i&d(B";
$dbname = "adventis_visita";

//Locals
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM locals ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $locals = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}

//GET
if (!empty($_GET["local"])) {

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        //Local
        $id = $_GET['local'];

        $sql = "SELECT * FROM locals WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $local = $stmt->fetch(PDO::FETCH_OBJ);

        //Persons
        if ($local) {
            $sql = "SELECT * FROM registros_visitas WHERE local = :local";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':local', $local->id, PDO::PARAM_INT);
            $stmt->execute();

            $persons = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        //Status
        if (isset($_GET["status"])) {
            $status = $local->status == 1 ? 0 : 1;
            $sql = "UPDATE locals SET status = :status WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $local->id, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("Location: ./?local={$local->id}");
                exit();
            }
        }

        //Prize Draw
        if (isset($_GET["toDraw"])) {


            if ($_GET["toDraw"] == 0) {
                $toDraw = false;
            } else {
                if (filter_var($_GET["toDraw"], FILTER_VALIDATE_INT) !== false) {
                    $sql = "SELECT * FROM registros_visitas WHERE id = :id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $_GET["toDraw"], PDO::PARAM_INT);
                    $stmt->execute();
                    $toDraw = $stmt->fetch(PDO::FETCH_OBJ);
                } else {
                    //All Elegible People
                    $sql = "SELECT * FROM registros_visitas WHERE local = :local AND chosen IS NULL";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':local', $local->id, PDO::PARAM_INT);
                    $stmt->execute();

                    //To Draw
                    $peopleToDraw = $stmt->fetchAll(PDO::FETCH_OBJ);
                    if ($peopleToDraw) {
                        $toDraw = $peopleToDraw[array_rand($peopleToDraw)];
                        $sql = "UPDATE registros_visitas SET chosen = 1 WHERE id = :id";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':id', $toDraw->id, PDO::PARAM_INT);
                        $stmt->execute();

                        header("Location: ./?local={$local->id}&toDraw={$toDraw->id}");
                        exit();
                    } else {
                        header("Location: ./?local={$local->id}&toDraw=0");
                        exit();
                    }
                }
            }
        }

        //Clean Prize Draw
        if (isset($_GET["toDrawClean"])) {
            $sql = "UPDATE registros_visitas SET chosen = NULL WHERE local = :local";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':local', $local->id, PDO::PARAM_INT);
            $stmt->execute();
            header("Location: ./?local={$local->id}");
            exit();
        }
    } catch (PDOException $e) {
        echo "Erro na conexão: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Título a definir">
    <meta name="author" content="UNIAENE">
    <title>UNIAENE - Centro Universitário Adventista de Ensino do Nordeste</title>


    <!--Favicon-->
    <link rel="apple-touch-icon" sizes="180x180" href="../img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon/favicon-16x16.png">
    <link rel="manifest" href="../img/favicon/site.webmanifest">
    <link rel="mask-icon" href="../img/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,500,600" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/menu.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/vendors.css" rel="stylesheet">

    <!-- MODERNIZR MENU -->
    <script src="../js/modernizr.js"></script>

    <style>
        .radio-inline {
            display: flex;
            align-items: center;
            /* Alinha o texto ao centro verticalmente */
        }

        .radio-inline input {
            margin-right: 10px;
            /* Espaçamento entre o botão e o texto */
        }

        .accordion-background {
            background-color: #cfe2ff;
        }

        .margin-top-persons {
            align-self: flex-start;
            margin-top: 50px;
        }

    </style>

</head>

<body>

    <div id="preloader">
        <div data-loader="circle-side"></div>
    </div><!-- /Preload -->

    <div id="loader_form">
        <div data-loader="circle-side-2"></div>
    </div><!-- /loader_form -->

    <nav>
        <ul class="cd-primary-nav">
            <li><a href="https://www.adventista.edu.br/" target="_blank" class="animated_link">Site Oficial</a></li>
            <li><a href="https://www.instagram.com/uniaene" target="_blank" class="animated_link">Instagram</a></li>
            <li><a href="https://www.youtube.com/channel/UC6GjhC0KktsGmiPtJ92mVdQ" target="_blank" class="animated_link">YouTube</a></li>
            <li><a href="https://www.facebook.com/FaculdadeAdventista" target="_blank" class="animated_link">Facebook</a></li>
            <li><a href="https://twitter.com/fac_adventista" target="_blank" class="animated_link">X (Twitter)</a></li>
        </ul>
    </nav>
    <!-- /menu -->

    <div class="container-fluid full-height">
        <div class="row row-height">
            <div class="col-lg-4 content-left image-left" style="overflow: inherit;">
                <div class="content-left-wrapper">
                    <a href="./" id="logo" class="d-none d-sm-block">
                        <img src="../assets/images/logo-azul.png" alt="" height="70">
                    </a>
                    <a href="./" id="logo" class="d-block d-sm-none">
                        <img src="../assets/images/logo-azul.png" alt="" height="35">
                    </a>

                    <div id="social">
                        <ul>
                            <li><a href="https://www.instagram.com/uniaene" target="_blank"><i class="bi bi-instagram text-dark"></i></a></li>
                            <li><a href="https://www.youtube.com/channel/UC6GjhC0KktsGmiPtJ92mVdQ" target="_blank"><i class="bi bi-youtube text-dark"></i></a></li>
                            <li><a href="https://www.facebook.com/FaculdadeAdventista" target="_blank"><i class="bi bi-facebook text-dark"></i></a></li>
                            <!--<li><a href="https://twitter.com/fac_adventista" target="_blank"><i class="bi bi-twitter-x text-dark"></i></a></li>-->
                        </ul>
                    </div>
                    <!-- /social -->
                    <div class="<?= !empty($persons) ? "margin-top-persons" : "" ?>">
                        <h4>Painel Administrativo</h4>
                        <a href="javascript:void(0);" class="btn_1 rounded d-inline-block m-0 mb-2" style="background: yellow; color: black;" data-bs-toggle="modal" data-bs-target="#qrcode"><i class="bi bi-qr-code"></i> Exibir QR Code</a>
                        <a href="javascript:void(0);" class="btn_1 rounded d-inline-block m-0 mb-2" style="background: yellow; color: black;" data-bs-toggle="modal" data-bs-target="#createLocal"><i class="bi bi-plus"></i> Novo Local</a>
                        <!-- Large button groups (default and split) -->
                        <!-- Example single danger button -->
                        <div class="btn-group w-100 rounded">
                            <button type="button" class="btn btn-primary dropdown-toggle rounded" data-bs-toggle="dropdown" aria-expanded="false">
                                Escolha o local desejado
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item text-success" href="javascriot:void(0);"><i class="bi bi-check-square"></i> Locais ativos</a></li>
                                <?php foreach ($locals as $localLink): ?>
                                    <?php if ($localLink->status == 1): ?>
                                        <li><a class="dropdown-item" href="./?local=<?= $localLink->id ?>"><?= $localLink->local ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="javascriot:void(0);"><i class="bi bi-x-square"></i> Locais Inativos</a></li>
                                <?php foreach ($locals as $localLink): ?>
                                    <?php if ($localLink->status == 0): ?>
                                        <li><a class="dropdown-item text-muted" href="./?local=<?= $localLink->id ?>"><?= $localLink->local ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="copy">©<?= date("Y") ?> UNIAENE</div>
                </div>
                <!-- /content-left-wrapper -->
            </div>
            <!-- /content-left -->
            <div class="col-lg-8">
                <?php if (empty($local)): ?>
                    <div class="d-flex justify-content-center align-items-center vh-100">
                        <h5 class="text-center">Escolha o local para iniciar</h5>
                    </div>
                <?php else: ?>
                    <nav class="navbar navbar-expand-lg bg-body-tertiary">
                        <div class="container-fluid">
                            <a class="navbar-brand pe-3 border-end" href="#"><?= $local->status == 0 ? "<span class='text-danger fw-bold'>[INATIVO]</span>" : "" ?> <?= $local->local ?></a>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                    <li class="nav-item dropdown me-3">
                                        <a class="dropdown-toggle btn btn-outline-primary" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Sorteio
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="./?local=<?= $local->id ?>&toDraw=true">Sortear</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item" href="./?local=<?= $local->id ?>&toDrawClean=true">Limpar Sorteio</a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="dropdown-toggle btn btn-outline-primary" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Local <?= $local->status == 1 ? "Ativo" : "Inativo" ?>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="./?local=<?= $local->id ?>&status=true"><?= $local->status == 1 ? "Desativar" : "Ativar" ?></a></li>
                                        </ul>
                                    </li>
                                </ul>
                                <form class="d-flex" role="search">
                                    <!--<input class="form-control me-2" type="search" placeholder="Busca" aria-label="Busca">-->
                                </form>
                            </div>
                        </div>
                    </nav>

                    <h5 class="py-3">Inscritos ( <?= !empty($persons) ? count($persons) : 0 ?> )</h5>

                    <div class="accordion" id="accordionExample">
                        <?php if (empty($persons)): ?>
                            <div class="alert alert-primary text-center" role="alert">
                                Nenhum inscrito ainda.
                            </div>
                        <?php endif; ?>
                        <?php foreach ($persons as $person): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <a href="javascript:void(0);" class="accordion-button collapsed <?= $person->chosen ? "text-success" : "" ?>" type="button" data-bs-toggle="collapse" data-bs-target="#person<?= $person->id ?>" aria-expanded="true" aria-controls="person<?= $person->id ?>">
                                        <?= $person->chosen ? "[Sorteado]" : "" ?> <?= $person->fullname ?><br>
                                        <?= (new DateTime($person->created_at))->format('d/m/Y H:i') ?>
                                    </a>
                                </h2>
                                <div id="person<?= $person->id ?>" class="accordion-collapse collapse accordion-background" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ol class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-start accordion-background">
                                                <div class="ms-2 me-auto">
                                                    <div class="fw-bold">Nome</div>
                                                    <?= $person->fullname ?>
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-start accordion-background">
                                                <div class="ms-2 me-auto">
                                                    <div class="fw-bold">E-mail</div>
                                                    <?= $person->email ?>
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-start accordion-background">
                                                <div class="ms-2 me-auto">
                                                    <div class="fw-bold">WahstApp</div>
                                                    <?= $person->whatsapp ?>
                                                </div>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-start accordion-background">
                                                <div class="ms-2 me-auto">
                                                    <div class="fw-bold">Curso de Interesse</div>
                                                    <?= $person->course ?>
                                                </div>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- /content-right-->
        </div>
        <!-- /row-->
    </div>
    <!-- /container-fluid -->

    <div class="cd-overlay-nav">
        <span></span>
    </div>
    <!-- /cd-overlay-nav -->

    <div class="cd-overlay-content">
        <span></span>
    </div>
    <!-- /cd-overlay-content -->

    <div class="modal fade" id="createLocal" tabindex="-1" aria-labelledby="createLocal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="./local/" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Novo Local</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="alert alert-warning alert-dismissible fade show d-none" role="alert">
                            <span></span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>


                        <div class="mb-3">
                            <label for="local" class="col-form-label">Local:</label>
                            <input type="text" name="local" class="form-control" id="local" placeholder="Digite o nome do local a ser visitado">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal::To Draw -->
        <div class="modal fade" id="qrcode" tabindex="-1" aria-labelledby="createLocal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="./local/" method="post">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">QR Code</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">

                            <div class="alert alert-warning alert-dismissible fade show d-none" role="alert">
                                <span></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>

                            <img src="../img/qr-publico.png" width="250px">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php if (isset($toDraw)): ?>
        <!-- Modal::To Draw -->
        <div class="modal fade" id="toDraw" tabindex="-1" aria-labelledby="createLocal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="./local/" method="post">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Sorteio</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">

                            <div class="alert alert-warning alert-dismissible fade show d-none" role="alert">
                                <span></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>

                            <?php if ($toDraw): ?>
                                <h1><?= $toDraw->fullname ?></h1>
                                <script src="https://cdn.lordicon.com/lordicon.js"></script>
                                <lord-icon
                                    src="https://cdn.lordicon.com/gqjpawbc.json"
                                    trigger="loop"
                                    delay="1500"
                                    state="in-reveal"
                                    style="width:150px;height:150px">
                                </lord-icon>
                                <h5>Parabéns</h5>
                            <?php else: ?>
                                <h5>Ninguém disponivel para o sorteio!</h5>
                            <?php endif; ?>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- COMMON SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="../js/common_scripts.min.js"></script>
    <script src="../js/velocity.min.js"></script>
    <script src="../js/functions.js"></script>
    <script src="../js/pw_strenght.js"></script>

    <!-- Wizard script -->
    <script src="../js/registration_func.js"></script>

    <script>
        //Form Ajax
        $('form').on('submit', function(event) {
            event.preventDefault();

            var $form = $(this);
            var url = $form.attr('action');
            var $alert = $('.alert.alert-warning');

            $.ajax({
                url: url,
                type: 'POST',
                data: $form.serialize(),
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.success == false) {
                        $alert.find('span').text(response.message);
                        $alert.removeClass('d-none');
                    } else {
                        window.location.href = './?local=' + response.success;
                    }
                },
                error: function(xhr, status, error) {
                    // Lida com erros
                    $alert.find('span').text('<p>Ocorreu um erro: ' + error + '</p>');
                }
            });
        });

        //To Draw
        let toDrawDiv = document.getElementById('toDraw');
        if (toDrawDiv) {
            var modal = new bootstrap.Modal(toDrawDiv);
            modal.show();
        }
    </script>

</body>

</html>