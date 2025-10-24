<?php

/**
 * UNIAENE - Painel Administrativo de Captação e Sorteio
 * Otimizado com Redis + PDO Persistente
 * 
 * by Fernando Issler + GPT-5
 */

$servername = "localhost";
$username   = "adventis_visita";
$password   = "UM2u+k]i&d(B";
$dbname     = "adventis_visita";

$local = null;
$persons = [];
$toDraw = null;

// 🔹 Conexão com Redis (cache de locais)
try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $locals_json = $redis->get('locals_cache');
    if ($locals_json) {
        $locals = json_decode($locals_json);
    } else {
        $conn = new PDO(
            "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true
            ]
        );

        $stmt = $conn->query("SELECT * FROM locals ORDER BY local ASC");
        $locals = $stmt->fetchAll(PDO::FETCH_OBJ);
        $redis->setex('locals_cache', 300, json_encode($locals));
    }
} catch (Exception $e) {
    error_log("Erro Redis: " . $e->getMessage());
    // fallback MySQL
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
        $stmt = $conn->query("SELECT * FROM locals ORDER BY local ASC");
        $locals = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e2) {
        die("Erro de conexão: " . $e2->getMessage());
    }
}

// 🔹 Operações por GET
try {
    if (!empty($_GET["local"])) {
        $id = (int) $_GET["local"];

        // Local atual
        $stmt = $conn->prepare("SELECT * FROM locals WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $local = $stmt->fetch(PDO::FETCH_OBJ);

        if ($local) {
            // Lista de inscritos
            $stmt = $conn->prepare("SELECT * FROM registros_visitas WHERE local = :local ORDER BY id DESC");
            $stmt->execute([':local' => $local->id]);
            $persons = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        // Alternar status
        if (isset($_GET["status"])) {
            $newStatus = $local->status == 1 ? 0 : 1;
            $stmt = $conn->prepare("UPDATE locals SET status = :s WHERE id = :i");
            $stmt->execute([':s' => $newStatus, ':i' => $local->id]);
            $redis->del('locals_cache');
            header("Location: ./?local={$local->id}");
            exit;
        }

        // Sorteio
        if (isset($_GET["toDraw"])) {
            if ($_GET["toDraw"] == 0) {
                $toDraw = false;
            } else {
                if (is_numeric($_GET["toDraw"])) {
                    $stmt = $conn->prepare("SELECT * FROM registros_visitas WHERE id = :id");
                    $stmt->execute([':id' => $_GET["toDraw"]]);
                    $toDraw = $stmt->fetch(PDO::FETCH_OBJ);
                } else {
                    $stmt = $conn->prepare("SELECT * FROM registros_visitas WHERE local = :local AND chosen IS NULL");
                    $stmt->execute([':local' => $local->id]);
                    $pool = $stmt->fetchAll(PDO::FETCH_OBJ);

                    if ($pool) {
                        $toDraw = $pool[array_rand($pool)];
                        $stmt = $conn->prepare("UPDATE registros_visitas SET chosen = 1 WHERE id = :id");
                        $stmt->execute([':id' => $toDraw->id]);
                        header("Location: ./?local={$local->id}&toDraw={$toDraw->id}");
                        exit;
                    } else {
                        header("Location: ./?local={$local->id}&toDraw=0");
                        exit;
                    }
                }
            }
        }

        // Limpar sorteio
        if (isset($_GET["toDrawClean"])) {
            $stmt = $conn->prepare("UPDATE registros_visitas SET chosen = NULL WHERE local = :local");
            $stmt->execute([':local' => $local->id]);
            header("Location: ./?local={$local->id}");
            exit;
        }
    }
} catch (PDOException $e) {
    error_log("Erro no painel: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel UNIAENE - Administração</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/vendors.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,500,600" rel="stylesheet">
    <script src="../js/modernizr.js"></script>
</head>

<body>

    <div class="container-fluid full-height">
        <div class="row row-height">
            <!-- Sidebar -->
            <div class="col-lg-4 content-left image-left" style="overflow: inherit;">
                <div class="content-left-wrapper">
                    <img src="../assets/images/logo-azul.png" alt="UNIAENE" height="70" class="mb-3">
                    <h4>Painel Administrativo</h4>

                    <a href="javascript:void(0);" class="btn_1 rounded mb-2" data-bs-toggle="modal" data-bs-target="#qrcode" style="background:yellow;color:black;"><i class="bi bi-qr-code"></i> QR Code</a>
                    <a href="javascript:void(0);" class="btn_1 rounded mb-2" data-bs-toggle="modal" data-bs-target="#createLocal" style="background:yellow;color:black;"><i class="bi bi-plus"></i> Novo Local</a>

                    <div class="btn-group w-100 rounded">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">Escolha o local</button>
                        <ul class="dropdown-menu">
                            <li class="dropdown-header text-success">Ativos</li>
                            <?php foreach ($locals as $l): if ($l->status == 1): ?>
                                    <li><a class="dropdown-item" href="./?local=<?= $l->id ?>"><?= htmlspecialchars($l->local) ?></a></li>
                            <?php endif;
                            endforeach; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li class="dropdown-header text-danger">Inativos</li>
                            <?php foreach ($locals as $l): if ($l->status == 0): ?>
                                    <li><a class="dropdown-item text-muted" href="./?local=<?= $l->id ?>"><?= htmlspecialchars($l->local) ?></a></li>
                            <?php endif;
                            endforeach; ?>
                        </ul>
                    </div>

                    <div class="copy mt-4">©<?= date("Y") ?> UNIAENE</div>
                </div>
            </div>

            <!-- Conteúdo principal -->
            <div class="col-lg-8">
                <?php if (empty($local)): ?>
                    <div class="d-flex justify-content-center align-items-center vh-100">
                        <h5>Escolha um local para iniciar</h5>
                    </div>
                <?php else: ?>
                    <nav class="navbar navbar-expand-lg bg-body-tertiary">
                        <div class="container-fluid">
                            <a class="navbar-brand pe-3 border-end" href="#">
                                <?= $local->status == 0 ? "<span class='text-danger fw-bold'>[INATIVO]</span>" : "" ?> <?= htmlspecialchars($local->local) ?>
                            </a>
                            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item dropdown me-3">
                                    <a class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">Sorteio</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="./?local=<?= $local->id ?>&toDraw=true">Sortear</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="./?local=<?= $local->id ?>&toDrawClean=true">Limpar Sorteio</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                        <?= $local->status ? "Ativo" : "Inativo" ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="./?local=<?= $local->id ?>&status=true">
                                                <?= $local->status ? "Desativar" : "Ativar" ?>
                                            </a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <h5 class="py-3">Inscritos (<?= count($persons) ?>)</h5>

                    <div class="accordion" id="accordionExample">
                        <?php if (empty($persons)): ?>
                            <div class="alert alert-primary text-center">Nenhum inscrito ainda.</div>
                        <?php else: ?>
                            <?php foreach ($persons as $p): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed <?= $p->chosen ? 'text-success' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#p<?= $p->id ?>">
                                            <?= $p->chosen ? '[Sorteado] ' : '' ?><?= htmlspecialchars($p->fullname) ?> — <?= (new DateTime($p->created_at))->format('d/m/Y H:i') ?>
                                        </button>
                                    </h2>
                                    <div id="p<?= $p->id ?>" class="accordion-collapse collapse bg-light">
                                        <div class="accordion-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><b>Nome:</b> <?= htmlspecialchars($p->fullname) ?></li>
                                                <li class="list-group-item"><b>E-mail:</b> <?= htmlspecialchars($p->email) ?></li>
                                                <li class="list-group-item"><b>WhatsApp:</b> <?= htmlspecialchars($p->whatsapp) ?></li>
                                                <li class="list-group-item"><b>Curso:</b> <?= htmlspecialchars($p->course) ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Novo Local -->
    <div class="modal fade" id="createLocal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="./local/" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Local</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="local" class="form-control" placeholder="Nome do local" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div class="modal fade" id="qrcode" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img src="../img/qr-publico.png" width="250" alt="QR Público">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Resultado Sorteio -->
    <?php if (isset($toDraw)): ?>
        <div class="modal fade" id="toDraw" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content text-center">
                    <div class="modal-header">
                        <h5 class="modal-title">Sorteio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php if ($toDraw): ?>
                            <h1><?= htmlspecialchars($toDraw->fullname) ?></h1>
                            <lord-icon src="https://cdn.lordicon.com/gqjpawbc.json" trigger="loop" delay="1500" style="width:150px;height:150px"></lord-icon>
                            <h5>Parabéns!</h5>
                        <?php else: ?>
                            <h5>Ninguém disponível para o sorteio!</h5>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.post($(this).attr('action'), $(this).serialize(), function(r) {
                try {
                    let res = JSON.parse(r);
                    if (res.success) {
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                } catch (err) {
                    alert('Erro inesperado');
                }
            });
        });
        <?php if (isset($toDraw)): ?>
            var modal = new bootstrap.Modal(document.getElementById('toDraw'));
            modal.show();
        <?php endif; ?>
    </script>
</body>

</html>