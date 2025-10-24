<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitas a Escolas</title>

    <!-- font-awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- Bootstrap-5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">

    <!-- custom-styles -->
    <link rel="stylesheet" href="assets/css/styleNew.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/animation.css">
</head>

<body>
    <div class="wrapper">

        <main>
            <div class="logo">
                <div class="logo-icon">
                    <img src="assets/images/logo-azul.png" height="50" alt="BeRifma">
                </div>
                <div class="logo-text">

                </div>
            </div>
            <div class="container">
                <div class="wrapper">
                    <div class="row align-items-center">
                        <div class="col-md-5 tab-sm-100 order_2">
                            <article class="side-text">
                                <h2>UNIAENE
                                </h2>
                                <p>Visite nosso site <span><a href="https://www.adventista.edu.br/" target="_blank">adventista.edu.br</a></span></p>
                                <a href="https://www.instagram.com/uniaene/" class="btn btn-info p-4 px-5 text-center" target="_blank">
                                    <i class="fa-brands fa-instagram"></i> Siga-nos no Instagram
                                </a>
                            </article>
                        </div>
                        <div class="col-md-7 tab-sm-100 order_1">
                            <form class="scroll-form" id="steps" method="post">
                                <div id="step1" class="steps-inner">
                                    <div class="event_details lightSpeedIn">
                                        <div class="event_detail-inner">
                                            <div class="main-heading">
                                                Seus Dados
                                            </div>

                                            <div class="text-input">
                                                <label for="name">Nome</label>
                                                <input type="text" name="name" id="name" placeholder="Digite seu nome" required>
                                                <i class="fa-solid fa-user"></i>
                                            </div>

                                            <div class="text-input mb-0">
                                                <label for="whatsapp">WhatsApp</label>
                                                <input type="text" name="whatsapp" id="whatsapp" placeholder="(00) 0000-0000" required>
                                                <i class="fa-solid fa-phone"></i>
                                            </div>
                                            <span class="text-muted small ms-4">Pode ser o whatsapp dos responsaáveis</span>


                                            <div class="text-input mt-3">
                                                <label for="mail-email">E-mail</label>
                                                <input type="text" name="mail" id="mail-email" placeholder="E-mail" required>
                                                <i class="fa-solid fa-envelope"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="other-input lightSpeedIn">
                                        <div class="main-heading">
                                            O que você acha de estudar no UNIAENE? (opcional)
                                        </div>
                                        <textarea name="description" id="description"></textarea>
                                        <div class="video_call_opt event_color">
                                            <div class="event-text">
                                                Curso de interesse
                                            </div>
                                            <div class="colors">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="purple"> Administração
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="yellow"> Ciências Contábeis
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="red"> Direito
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="green"> Enfermagem
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="red"> Fisioterapia
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="green"> Gastronomia
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="green"> Teologia
                                                        </label><br>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="purple"> Gestão da TI
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="yellow"> Medicina Veterinária
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="red"> Nutrição
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="green"> Odontologia
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="red"> Pedagogia
                                                        </label><br>
                                                        <label class="radio-inline">
                                                            <input class="green" type="radio" name="event_color" value="green"> Psicologia
                                                        </label><br>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="submit">
                                            <button type="button" class="sub" id="sub">Enviar
                                                <span><i class="fa-solid fa-thumbs-up"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>








        <div id="error">

        </div>
    </div>


    <!-- Bootstrap-5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <!-- My js -->
    <script src="assets/js/custom.js"></script>
</body>

</html>