<?php
require("./php/hidephp.php");
session_name("HATIDS");
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => "",
    'secure' => true,
    'httponly' => false,
    'samesite' => 'None'
]);
session_start();
date_default_timezone_set('America/Sao_Paulo');
require('./php/connection.php');
require('./php/functions.php');

if (isset($_COOKIE['EMAIL']) && isset($_COOKIE['TYPE'])) {
    $email = $_COOKIE['EMAIL'];
    $type = $_COOKIE['TYPE'];
} else if (isset($_SESSION['EMAIL']) && isset($_SESSION['TYPE'])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {
        expiredReturn();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
    $email = $_SESSION['EMAIL'];
    $type = $_SESSION['TYPE'];
} else {
    expiredReturn();
}

if (!isset($_GET['ids'])) {
    header("Location: /" . strtolower($type) . "menu.php");
    exit();
}
$ids = $_GET['ids'];

$rowser = mysqli_fetch_assoc(searchServices($ids, $conn));
$rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));

if (is_null($rowuser)) {
    expiredReturn();
}

if (is_null($rowser)) {
    header("Location: /" . strtolower($type) . "menu/");
    exit();
}

$id = $rowuser["ID_$type"];
$iddev = $rowser['COD_DEVELOPER'];
$idcus = $rowser['COD_CUSTOMER'];


if ($type == "CUSTOMER") {
    if ($idcus !== $id) {
        header("Location: /customermenu/");
        exit();
    }
} elseif ($type == "DEVELOPER" and $rowser['STATUS'] >= 1) {
    if ($iddev !== $id) {
        header("Location: /developermenu/");
        exit();
    }
}

$infodev = searchInfoDev($iddev, $conn);
$infocus = searchInfoCus($idcus, $conn);
$developer_exist = 0;
$service_exist = 0;

if ($rowser['STATUS'] >= 1) {
    $birthdev = explode("-", $infodev['BIRTH_DATE']);
    $infodev['BIRTH_DATE'] = $birthdev[2] . "/" . $birthdev[1] . "/" . $birthdev[0];
}

$birthcus = explode("-", $infocus['BIRTH_DATE']);
$infocus['BIRTH_DATE'] = $birthcus[2] . "/" . $birthcus[1] . "/" . $birthcus[0];

if (isset($_POST['REQUEST'])) {
    $status = $rowser['STATUS'];

    if ($status >= 1) {
        $_SESSION['servicemsg'] = "O serviço já foi solicitado por outro desenvolvedor! Por favor, solicite outro serviço!";
        $_SESSION['serviceclass'] = "is-warning";
        header("Location: /pendingservices/");
        exit();
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET COD_DEVELOPER = ?, STATUS = 1 WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt, "is", $id, $ids);
        $bool = mysqli_stmt_execute($stmt);

        if ($bool) {
            $_SESSION['servicemsg'] = "Solicitação enviada com sucesso! Por favor, aguarde a confirmação do cliente!";
            $_SESSION['serviceclass'] = "is-success";
            header("Location: /pendingservices/");
            exit();
        } else {
            $_SESSION['servicemsg'] = "Falha ao enviar a solicitação! Por favor, tente novamente mais tarde!";
            $_SESSION['serviceclass'] = "is-danger";
            header("Location: /pendingservices/");
            exit();
        }
    }
} else if (isset($_POST['SEND'])) {
    $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET STATUS = 2 WHERE ID_SERVICE = ?");
    mysqli_stmt_bind_param($stmt, "s", $ids);
    $bool = mysqli_stmt_execute($stmt);

    if ($bool) {
        $_SESSION['servicemsg'] = "A proposta de serviço foi aceita! Para começar o desenvolvimento, entre em contato com o desenvolvedor!";
        $_SESSION['serviceclass'] = "is-success";
        header("Location: /developmentservices/");
        exit();
    } else {
        $_SESSION['servicemsg'] = "A proposta de serviço não foi aceita! Por favor, tente novamente mais tarde!";
        $_SESSION['serviceclass'] = "is-danger";
        header("Location: /developmentservices/");
        exit();
    }
} else if (isset($_POST['SENDRECUSE'])) {
    $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET COD_DEVELOPER = NULL, STATUS = 0 WHERE ID_SERVICE = ?");
    mysqli_stmt_bind_param($stmt, "s", $ids);
    $bool = mysqli_stmt_execute($stmt);

    if ($bool) {
        $_SESSION['servicemsg'] = "A proposta de serviço foi recusada com sucesso!";
        $_SESSION['serviceclass'] = "is-success";
        header("Location: /pendingservices/");
        exit();
    } else {
        $_SESSION['servicemsg'] = "A proposta de serviço não foi recusada! Por favor, tente novamente mais tarde!";
        $_SESSION['serviceclass'] = "is-danger";
        header("Location: /pendingservices/");
        exit();
    }
    //codigo do report, com verificação se o developer já reportou
} elseif (isset($_POST['REPORT'])) {
    $idss = $rowser['ID_SERVICE'];
    $result_report = mysqli_query($conn, "SELECT COD_DEVELOPER FROM TB_REPORT WHERE COD_DEVELOPER = '$id' AND COD_SERVICE = '$idss'");
    if (mysqli_num_rows($result_report) == 1) {
        $developer_exist = 1;
    } else {
        $developer_exist = 2;
        $type_report = $_POST['cont'];
        $stmt = mysqli_prepare($conn, "INSERT INTO TB_REPORT(COD_SERVICE, COD_DEVELOPER,TYPE_REPORT) VALUES (?,?,?)");
        mysqli_stmt_bind_param($stmt, "sss", $idss, $id, $type_report);
        mysqli_stmt_execute($stmt);
    }
    //codigo do rating, com verificação se o serviço já foi avaliado
} elseif (isset($_POST['RATING'])) {
    $id_ratings = $rowser['ID_SERVICE'];
    $result_rating = mysqli_query($conn, "SELECT COD_SERVICE FROM TB_RATING WHERE COD_CUSTOMER = '$id' AND COD_SERVICE = '$id_ratings'");
    if (mysqli_num_rows($result_rating) == 1) {
        $service_exist = 1;
    } else {
        $service_exist = 2;
        $note = $_POST['av'];
        $review = $_POST['review'];
        $stmt = mysqli_prepare($conn, "INSERT INTO TB_RATING(COD_SERVICE, COD_CUSTOMER, NOTE, REVIEW) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $id_ratings, $id, $note, $review);
        mysqli_stmt_execute($stmt);
    }
}
if (isset($_POST['CONCLUDED'])) {
    $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET  STATUS = 3 WHERE ID_SERVICE = ?");
    mysqli_stmt_bind_param($stmt, "s", $ids);
    mysqli_stmt_execute($stmt);
    header("Location: /customermenu/");
}
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hatchfy</title>
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/vue.js"></script>
    <script src="/js/jscript.js"></script>
    <script src="/js/v-mask.min.js"></script>
    <script src="/js/moment.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php if ($type == "CUSTOMER") {
            require("./headercustomer.php");
        } else {
            require("./headerdeveloper.php");
        } ?>
        <br>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <section class="hero is-dark">
                        <div class="hero-body is-dark">
                            <p class="title">
                                Detalhes do serviço
                            </p>
                        </div>
                    </section>
                    <form action="" method="POST">
                        <div class="section">
                            <div class="columns">
                                <div class="column is-5">
                                    <div class="field">
                                        <label class="label">Título do serviço</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $rowser['TITLE']; ?></p>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Descrição do serviço</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $rowser['DESCRIPTION']; ?></p>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Categoria</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $rowser['NAME']; ?></p>
                                        </div>
                                    </div>
                                    <br>
                                    <?php if ($rowser['STATUS'] == 0 and $type == "DEVELOPER") { ?>
                                        <a class="button is-danger is-medium" @click="onClickButtonModal">Reportar</a>
                                        <div class="modal" :class="{'is-active': isActiveModal}">
                                            <div class="modal-background"></div>
                                            <div class="modal-card">
                                                <header class="modal-card-head">
                                                    <p class="modal-card-title">Reportar o Serviço</p>
                                                    <button type="button" class="delete" aria-label="close" @click="onClickButtonModal"></button>
                                                </header>
                                                <section class="modal-card-body">
                                                    <div class="control">
                                                        <label class="radio is-large">
                                                            <input type="radio" name="cont" value="cont-sexual">
                                                            Conteúdo sexual
                                                        </label>
                                                        <br>
                                                        <label class="radio">
                                                            <input type="radio" name="cont" value="cont-violence">
                                                            Conteúdo violento ou repulsivo
                                                        </label>
                                                        <br>
                                                        <label class="radio">
                                                            <input type="radio" name="cont" value="cont-rate-abuse">
                                                            Conteúdo de incitação ao ódio ou abusivo
                                                        </label>
                                                        <br>
                                                        <label class="radio">
                                                            <input type="radio" name="cont" value="cont-spam">
                                                            Spam ou enganoso
                                                        </label>
                                                        <br>
                                                        <label class="radio">
                                                            <input type="radio" name="cont" value="cont-other">
                                                            Outro
                                                        </label>
                                                        <br>
                                                        <button type="submit" class="button" name="REPORT">Enviar Denúncia</button>
                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                        <br>
                                        <!-- codigo da mensagem do report caso o developer já tenha reportado e quando o report é feito-->
                                        <?php if ($developer_exist == 1) { ?>
                                            <article class="message is-danger">
                                                <div class="message-header">
                                                    <p>Aviso</p>
                                                    <button class="delete" aria-label="delete"></button>
                                                </div>
                                                <div class="message-body">
                                                    Você já reportou esse serviço.
                                                </div>
                                            </article>
                                        <?php } else if ($developer_exist == 2) { ?>
                                            <article class="message is-sucess">
                                                <div class="message-header">
                                                    <p>Aviso</p>
                                                    <button class="delete" aria-label="delete"></button>
                                                </div>
                                                <div class="message-body">
                                                    Reporte feito com sucesso.
                                                </div>
                                            </article>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <ul class="steps has-content-centered is-large">
                                <li class="steps-segment <?php if($rowser['STATUS'] == 0){
                                    echo "is-active";
                                }?>">
                                    <span class="steps-marker">
                                        <span class="icon">
                                            <i class="fa fa-question"></i>
                                        </span>
                                    </span>
                                    <div class="steps-content">
                                        <p class="is-size-5 has-text-weight-bold">Aguardando desenvolvedor</p>
                                    </div>
                                </li>
                                <li class="steps-segment <?php if($rowser['STATUS'] == 1){
                                    echo "is-active";
                                }?>">
                                    <span class="steps-marker">
                                        <span class="icon">
                                            <i class="fa fa-user-clock"></i>
                                        </span>
                                    </span>
                                    <div class="steps-content">
                                        <p class="is-size-5 has-text-weight-bold">Aguardando resposta</p>
                                    </div>
                                </li>
                                <li class="steps-segment <?php if($rowser['STATUS'] == 2){
                                    echo "is-active";
                                }?>">
                                    <span class="steps-marker">
                                        <span class="icon">
                                            <i class="fa fa-spinner"></i>
                                        </span>
                                    </span>
                                    <div class="steps-content">
                                        <p class="is-size-5 has-text-weight-bold">Serviço em desenvolvimento</p>
                                    </div>
                                </li>
                                <li class="steps-segment <?php if($rowser['STATUS'] == 3){
                                    echo "is-active";
                                }?>">
                                    <span class="steps-marker">
                                        <span class="icon">
                                            <i class="fa fa-check"></i>
                                        </span>
                                    </span>
                                    <div class="steps-content">
                                        <p class="is-size-5 has-text-weight-bold">Serviço Concluído</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <?php if ($type == "CUSTOMER") { ?>
                            <section class="hero is-dark">
                                <div class="hero-body is-dark">
                                    <p class="title">
                                        Informações do desenvolvedor
                                    </p>
                                </div>
                            </section>
                            <div class="section">
                                <?php if ($rowser['STATUS'] >= 1) { ?>
                                    <div class="columns">
                                        <div class="column is-3">
                                            <div class="field">
                                                <label class="label has-text-centered"> Foto do desenvolvedor </label>
                                                <figure class="image is-square">
                                                    <img style="object-fit: cover;" class="is-rounded" src="<?php echo $infodev['IMAGE'] ?>">
                                                </figure>
                                                <br>
                                                <?php if ($rowser['STATUS'] == 3) { ?>
                                                    <?php if ($service_exist == 0) { ?>
                                                        <div class="buttons is-centered"><button type="button" class="button is-success is-medium" @click="onClickButtonModal">Avaliar</button></div>
                                                        <div class="modal" :class="{'is-active': isActiveModal}">
                                                            <div class="modal-background"></div>
                                                            <div class="modal-card">
                                                                <header class="modal-card-head">
                                                                    <p class="modal-card-title">Avaliar Serviço</p>
                                                                    <button class="delete" type="button" aria-label="close" @click="onClickButtonModal"></button>
                                                                </header>
                                                                <section class="modal-card-body has-text-centered">
                                                                    <div class="estrelas">
                                                                        <input type="radio" id="cm_star-empty" name="av" value="" checked />
                                                                        <label for="cm_star-1"><i class="fa fa-star fa-3x"></i></label>
                                                                        <input type="radio" id="cm_star-1" name="av" value="1" />
                                                                        <label for="cm_star-2"><i class="fa fa-star fa-3x"></i></label>
                                                                        <input type="radio" id="cm_star-2" name="av" value="2" />
                                                                        <label for="cm_star-3"><i class="fa fa-star fa-3x"></i></label>
                                                                        <input type="radio" id="cm_star-3" name="av" value="3" />
                                                                        <label for="cm_star-4"><i class="fa fa-star fa-3x"></i></label>
                                                                        <input type="radio" id="cm_star-4" name="av" value="4" />
                                                                        <label for="cm_star-5"><i class="fa fa-star fa-3x"></i></label>
                                                                        <input type="radio" id="cm_star-5" name="av" value="5" />
                                                                    </div>
                                                                    <br>
                                                                    <label class="label" for="description">Sua review do serviço</label>
                                                                    <textarea class="textarea has-fixed-size" placeholder="Digite aqui" name="review"></textarea>
                                                                    <br>
                                                                    <button type="submit" class="button" name="RATING">Avaliar</button>
                                                                </section>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <!-- codigo da mensagem de avaliação quando a avaliação já foi feita ou quando é a primeira vez -->
                                                    <?php if ($service_exist == 2) { ?>
                                                        <article class="message is-success">
                                                            <div class="message-header">
                                                                <p>Aviso</p>
                                                                <button class="delete" aria-label="delete"></button>
                                                            </div>
                                                            <div class="message-body">
                                                                Serviço avaliado com sucesso.
                                                            </div>
                                                        </article>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <div class="field">
                                                <label class="label">Nome</label>
                                                <div class="box">
                                                    <p class="subtitle is-5"><?php echo $infodev['NAME']; ?></p>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label" for="description">Email</label>
                                                <div class="box">
                                                    <p class="subtitle is-5"><?php echo $infodev['EMAIL']; ?></p>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label" for="contact">Data de nascimento</label>
                                                <div class="box">
                                                    <p class="subtitle is-5"><?php echo $infodev['BIRTH_DATE']; ?></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                <?php } ?>
                                <?php if ($rowser['STATUS'] <= 0) { ?>
                                    <div class="box">
                                        <p class="title is-5"> Ainda não há um desenvolvedor! </p>
                                    </div>
                                <?php } ?>
                                <?php if ($rowser['STATUS'] == 1) { ?>
                                    <div class="section has-text-centered">
                                        <div class="field">
                                            <button class="button is-medium is-primary" name="SEND" type="submit">Aceitar pedido</button>
                                            <button class="button is-medium is-danger" name="SENDRECUSE" type="submit">Recusar pedido</button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($type == "DEVELOPER") { ?>
                            <section class="hero is-dark">
                                <div class="hero-body is-dark">
                                    <p class="title">
                                        Informações do cliente
                                    </p>
                                </div>
                            </section>
                            <div class="section">
                                <div class="columns">
                                    <div class="column is-3">
                                        <div class="field">
                                            <label class="label has-text-centered">Foto do cliente</label>
                                            <figure class="image is-square">
                                                <img style="object-fit: cover;" class="is-rounded" src="<?php echo $infocus['IMAGE']; ?>">
                                            </figure>
                                        </div>
                                    </div>
                                    <div class="column">
                                        <div class="field">
                                            <label class="label">Nome</label>
                                            <div class="box">
                                                <p class="subtitle is-5"><?php echo $infocus['NAME']; ?></p>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label">Email</label>
                                            <div class="box">
                                                <p class="subtitle is-5"><?php echo $infocus['EMAIL']; ?></p>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label">Data de nascimento</label>
                                            <div class="box">
                                                <p class="subtitle is-5"><?php echo $infocus['BIRTH_DATE']; ?></p>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label">Contato</label>
                                            <div class="box">
                                                <p class="subtitle is-5"><?php echo $infocus['CONTACT'] ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($rowser['STATUS'] == 0) { ?>
                                    <div class="section has-text-centered">
                                        <div class="field">
                                            <button class="button is-medium is-primary" name="REQUEST" type="submit"> Enviar solicitação </button>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($rowser['STATUS'] == 1) { ?>
                                    <div class="section has-text-centered">
                                        <div class="notification is-primary">
                                            <p class="title is-5"> Aguardando a confirmação pelo cliente...</p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($type == "CUSTOMER" && $rowser['STATUS'] == 2) { ?>
                            <div class="container has-text-centered">
                                <button class="button is-primary is-medium " type="submit" name="CONCLUDED">Concluir Serviço</button>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
            <?php require "baseboard.php" ?>
        </section>
    </div>
    <noscript>
        <style>
            .script {
                display: none;
            }
        </style>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container has-text-centered">
                    <div class="box has-text-centered">
                        <p class="title font-face"> JavaScript não habilitado! </p> <br>
                        <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p>
                    </div>
                </div>
            </div>
        </section>
    </noscript>
    <script>
        new Vue({
            el: '#app',
            data: {
                isActiveBurger: false,
                isActiveModal: false,
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger
                },
                onClickButtonModal() {
                    this.isActiveModal = !this.isActiveModal
                }
            }
        })
    </script>
</body>

</html>