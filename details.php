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
    } 
    else if (isset($_SESSION['EMAIL']) && isset($_SESSION['TYPE'])) {
        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {
            expiredReturn();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
        $email = $_SESSION['EMAIL'];
        $type = $_SESSION['TYPE'];
    } 
    else {
        expiredReturn();
    }

    if (!isset($_GET['ids']) || !isset($_GET['n']) || !isset($_GET['time'])) {
        header("Location: /");
        exit();
    }
    $ids = $_GET['ids'];
    $title = $_GET['n'];
    $creationDate = $_GET['time'];

    $rowser = mysqli_fetch_assoc(searchServices($ids, $title, $creationDate, $conn));
    $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));

    if (is_null($rowuser)) {
        expiredReturn();
    }

    if (is_null($rowser)) {
        http_response_code(404);
        include("./errors/404.html");
        die();
    }

    $id = $rowuser["ID_$type"];
    $iddev = $rowser['COD_DEVELOPER'];
    $idcus = $rowser['COD_CUSTOMER'];

    if ($type == "CUSTOMER") {
        if ($idcus !== $id) {
            header("Location: /customermenu/");
            exit();
        }
    } 
    elseif ($type == "DEVELOPER" and $rowser['STATUS'] >= 1) {
        if ($iddev !== $id) {
            header("Location: /search/");
            exit();
        }
    }

    $infodev = searchInfoDev($iddev, $conn);
    $infocus = searchInfoCus($idcus, $conn);

    if ($rowser['STATUS'] >= 1) {
        $birthdev = explode("-", $infodev['BIRTH_DATE']);
        $infodev['BIRTH_DATE'] = $birthdev[2] . "/" . $birthdev[1] . "/" . $birthdev[0];
    }

    $birthcus = explode("-", $infocus['BIRTH_DATE']);
    $infocus['BIRTH_DATE'] = $birthcus[2] . "/" . $birthcus[1] . "/" . $birthcus[0];

    if (isset($_POST['REQUEST'])) {
        $status = $rowser['STATUS'];

        if ($status >= 1) {
            $_SESSION['servicereturn'] = array("msg" => "O serviço já foi solicitado por outro desenvolvedor! Por favor, solicite outro serviço!","class" => "is-warning");
            header("Location: /pendingservices/");
            exit();
        } 
        else {
            $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET COD_DEVELOPER = ?, STATUS = 1 WHERE ID_SERVICE = ?");
            mysqli_stmt_bind_param($stmt, "is", $id, $ids);
            $bool = mysqli_stmt_execute($stmt);
            $boolmail = statusChanged($infocus['EMAIL'], "Atualização no status do serviço (".$rowser['TITLE'].").", "Você tem uma proposta de um desenvolver no serviço " . $rowser['TITLE']. "!");

            if ($bool && $boolmail) {
                $_SESSION['servicereturn'] = array("msg" => "Solicitação enviada com sucesso! Por favor, aguarde a confirmação do cliente!","class" => "is-success");
                header("Location: /pendingservices/");
                exit();
            } 
            else {
                $_SESSION['servicereturn'] = array("msg" => "Falha ao enviar a solicitação! Por favor, tente novamente mais tarde!","class" => "is-danger");
                header("Location: /pendingservices/");
                exit();
            }
        }
    } 
    else if (isset($_POST['SEND'])) {
        $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET STATUS = 2 WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt, "s", $ids);
        $bool = mysqli_stmt_execute($stmt);
        $boolmail = statusChanged($infodev['EMAIL'], "Atualização no status do serviço (".$rowser['TITLE'].").", "A sua proposta no serviço ".$rowser['TITLE'] ." foi aceita! Entre em contato com o cliente para iniciar o desenvolvimento!");

        if ($bool && $boolmail) {
            $_SESSION['servicereturn'] = array("msg" => "A proposta de serviço foi aceita! Para começar o desenvolvimento, entre em contato com o desenvolvedor!","class" => "is-success");
            header("Location: /developmentservices/");
            exit();
        } else {
            $_SESSION['servicereturn'] = array("msg" => "Não foi possivel aceitar a proposta de serviço! Por favor, tente novamente mais tarde!","class" => "is-danger");
            header("Location: /developmentservices/");
            exit();
        }
    } 
    else if (isset($_POST['SENDRECUSE'])) {
        $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET COD_DEVELOPER = NULL, STATUS = 0 WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt, "s", $ids);
        $bool = mysqli_stmt_execute($stmt);
        $boolmail = statusChanged($infodev['EMAIL'], "Atualização no status do serviço (".$rowser['TITLE'].").", "A sua proposta no serviço ".$rowser['TITLE'] ." foi recusada! Você pode selecionar outro serviço no nosso site.");

        if ($bool && $boolmail) {
            $_SESSION['servicereturn'] = array("msg" => "A proposta de serviço foi recusada com sucesso!","class" => "is-success");
            header("Location: /pendingservices/");
            exit();
            
        } else {
            $_SESSION['servicereturn'] = array("msg" => "Não foi possivel recusar a proposta de serviço. Por favor, tente novamente mais tarde!","class" => "is-danger");
            header("Location: /pendingservices/");
            exit();
        }
    } 
    elseif (isset($_POST['REPORT'])) {
        $result_report = mysqli_query($conn, "SELECT COD_DEVELOPER FROM TB_REPORT WHERE COD_DEVELOPER = '$id' AND COD_SERVICE = '$ids'");
        if (mysqli_num_rows($result_report) == 1) {
            $_SESSION['servicereturn'] = array("msg" => "Você já reportou esse serviço!","class" => "is-warning");
        } 
        else {
            $type_report = filter_input(INPUT_POST, "cont", FILTER_SANITIZE_STRING);
            $stmt = mysqli_prepare($conn, "INSERT INTO TB_REPORT(COD_SERVICE, COD_DEVELOPER, TYPE_REPORT) VALUES (?,?,?)");
            mysqli_stmt_bind_param($stmt, "sss", $ids, $id, $type_report);
            $bool = mysqli_stmt_execute($stmt);
            $boolmail = statusChanged($infocus['EMAIL'], "ATENÇÃO!!","O serviço ".$rowser['TITLE']." foi REPORTADO. Verifique se há algum conteúdo impróprio. Caso não haja, ignore esta mensagem.");
            
            if($bool) {
                $_SESSION['servicereturn'] = array("msg" => "O serviço foi reportado com sucesso!","class" => "is-success");
            }
            else {
                $_SESSION['servicereturn'] = array("msg" => "Falha ao reportar o serviço! Por favor, tente novamente mais tarde!","class" => "is-danger");
            }
            $result_report_limit = mysqli_query($conn, "SELECT COD_SERVICE FROM TB_REPORT WHERE COD_SERVICE = '$ids'");

            if(mysqli_num_rows($result_report_limit) >= 4){
              mysqli_query($conn, "DELETE FROM TB_SERVICES WHERE COD_SERVICE = '$ids'");
              statusChanged($infocus['EMAIL'], "Atualização do serviço (".$rowser['TITLE'].").", "O serviço ".$rowser['TITLE']." foi BANIDO. Você pode criar outro serviço no nosso site.");
            }
        }
    } 
    elseif (isset($_POST['RATING'])) {
        $resrat = mysqli_query($conn, "SELECT COD_SERVICE FROM TB_RATING WHERE COD_CUSTOMER = '$id' AND COD_SERVICE = '$ids'");
        if (mysqli_num_rows($resrat) == 1) {
            $_SESSION['servicereturn'] = array("msg" => "Você já avaliou esse desenvolvedor!","class" => "is-warning");
        } 
        else {
            $note = filter_input(INPUT_POST, "av", FILTER_SANITIZE_STRING);
            $review = filter_input(INPUT_POST, "review", FILTER_SANITIZE_STRING);
            $stmt = mysqli_prepare($conn, "INSERT INTO TB_RATING(COD_SERVICE, COD_CUSTOMER, NOTE, REVIEW) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $ids, $id, $note, $review);
            $bool = mysqli_stmt_execute($stmt);

            if($bool) {
                $_SESSION['servicereturn'] = array("msg" => "O desenvolvedor foi avaliado com sucesso!","class" => "is-success");
            }
            else {
                $_SESSION['servicereturn'] = array("msg" => "Falha ao enviar a sua avaliação! Por favor, tente novamente mais tarde!","class" => "is-danger");
            }
        }
    } 
    elseif(isset($_POST['CONCLUDED'])){
        $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET STATUS = 3 WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt, "s", $ids);
        $bool = mysqli_stmt_execute($stmt);
        $boolmail = statusChanged($infodev['EMAIL'], "Atualização no status do serviço (".$rowser['TITLE'].").", "O serviço ".$rowser['TITLE']." foi marcado como concluído pelo Cliente. Obrigado por utilizar nossos serviços.");

        if($bool && $boolmail) {
            $_SESSION['servicereturn'] = array("msg" => "O serviço foi concluído com sucesso! Caso deseje, você pode estar enviando uma avaliação ao desenvolvedor!","class" => "is-success");
        }
        else {
            $_SESSION['servicereturn'] = array("msg" => "Não foi possivel definir o serviço como concluído! Por favor, tente novamente mais tarde!","class" => "is-danger");
        }
        header("Location: /doneservices/");
        exit();
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
    <script src="/js/bulma-toast.min.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php if ($type == "CUSTOMER") {
            require("./headercustomer.php");
        } else {
            require("./headerdeveloper.php");
        } ?>
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
                                        <label class="label has-text-white">Título do serviço</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $rowser['TITLE']; ?></p>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label has-text-white">Categoria do serviço</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $rowser['NAME']; ?></p>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label has-text-white">Descrição do serviço</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $rowser['DESCRIPTION']; ?></p>
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
                                                        <br>
                                                        <button type="submit" class="button is-info" name="REPORT">Enviar Denúncia</button>
                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                        <br>
                                    <?php } ?>
                                </div>            
                            </div>
                            <?php if($type == "CUSTOMER"){ ?>
                                <ul class="steps has-content-centered is-large">
                                    <li class="steps-segment <?php if($rowser['STATUS'] == 0){echo "is-active";}?>">
                                        <span class="steps-marker">
                                            <span class="icon">
                                                <i class="fa fa-question"></i>
                                            </span>
                                        </span>
                                        <div class="steps-content">
                                            <p class="is-size-5 has-text-weight-bold has-text-white">Aguardando desenvolvedor</p>
                                        </div>
                                    </li>
                                    <li class="steps-segment <?php if($rowser['STATUS'] == 1){echo "is-active";}?>">
                                        <span class="steps-marker">
                                            <span class="icon">
                                                <i class="fa fa-user-clock"></i>
                                            </span>
                                        </span>
                                        <div class="steps-content">
                                            <p class="is-size-5 has-text-weight-bold has-text-white">Aguardando resposta</p>
                                        </div>
                                    </li>
                                    <li class="steps-segment <?php if($rowser['STATUS'] == 2){echo "is-active";}?>">
                                        <span class="steps-marker">
                                            <span class="icon">
                                                <i class="fa fa-spinner"></i>
                                            </span>
                                        </span>
                                        <div class="steps-content">
                                            <p class="is-size-5 has-text-weight-bold has-text-white">Serviço em desenvolvimento</p>
                                        </div>
                                    </li>
                                    <li class="steps-segment <?php if($rowser['STATUS'] == 3){echo "is-active";}?>">
                                        <span class="steps-marker">
                                            <span class="icon">
                                                <i class="fa fa-check"></i>
                                            </span>
                                        </span>
                                        <div class="steps-content">
                                            <p class="is-size-5 has-text-weight-bold has-text-white">Serviço Concluído</p>
                                        </div>
                                    </li>
                                </ul>
                            <?php } ?>
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
                                                <label class="label has-text-centered has-text-white"> Foto do desenvolvedor </label>
                                                <figure class="image is-square">
                                                    <img style="object-fit: cover;" class="is-rounded" src="../../../<?php echo $infodev['IMAGE'] ?>">
                                                </figure>
                                                <br>
                                                <?php if ($rowser['STATUS'] == 3) { ?>
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
                                                                <button type="submit" class="button is-info" name="RATING">Avaliar</button>
                                                            </section>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <div class="field">
                                                <label class="label has-text-white">Nome</label>
                                                <div class="box">
                                                    <p class="subtitle is-5"><?php echo $infodev['NAME']; ?></p>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label has-text-white">Email</label>
                                                <div class="box">
                                                    <p class="subtitle is-5"><?php echo $infodev['EMAIL']; ?></p>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label has-text-white">Data de nascimento</label>
                                                <div class="box">
                                                    <p class="subtitle is-5"><?php echo $infodev['BIRTH_DATE']; ?></p>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label class="label has-text-white">Telefone</label>
                                                <div class="box">
                                                    <p class="subtitle is-5"><?php echo $infodev['CONTACT']; ?></p>
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
                                    <div class="field is-grouped is-grouped-centered is-grouped-multiline">
                                        <p class="control">
                                            <button type="submit" name="SEND" class="button is-medium is-primary">Aceitar pedido</button>
                                        </p>
                                        <p class="control">
                                            <button type="submit" name="SENDRECUSE" class="button is-medium is-danger">Recusar pedido</button>
                                        </p>
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
                                            <label class="label has-text-centered has-text-white">Foto do cliente</label>
                                            <figure class="image is-square">
                                                <img style="object-fit: cover;" class="is-rounded" src="../../../<?php echo $infocus['IMAGE']; ?>">
                                            </figure>
                                        </div>
                                    </div>
                                    <div class="column">
                                        <div class="field">
                                            <label class="label has-text-white">Nome</label>
                                            <div class="box">
                                                <p class="subtitle is-5"><?php echo $infocus['NAME']; ?></p>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label has-text-white">Email</label>
                                            <div class="box">
                                                <p class="subtitle is-5"><?php echo $infocus['EMAIL']; ?></p>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label has-text-white">Data de nascimento</label>
                                            <div class="box">
                                                <p class="subtitle is-5"><?php echo $infocus['BIRTH_DATE']; ?></p>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label class="label has-text-white">Telefone</label>
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
                        <?php if($type == "CUSTOMER" && $rowser['STATUS'] == 2){?>
                            <div class="container has-text-centered">
                                <button class="button is-info is-medium" type="submit" name="CONCLUDED">Concluir Serviço</button>
                            </div>    
                        <?php }?>
                    </form>
                </div>
            </div>
        </section>
            <?php require "baseboard.php"?>
    </div>
    <noscript> <style> .script { display: none; } </style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
    <script>
    var vue = new Vue({
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
                },
                showMessage(message, messageclass, position) {
                    bulmaToast.toast({
                        message: message,
                        type: messageclass,
                        duration: 5000,
                        position: position,
                        dismissible: true,
                        pauseOnHover: true,
                        closeOnClick: false,
                        animate: {
                            in: 'fadeIn',
                            out: 'fadeOut'
                        },
                    })
                }
            }
        })
    </script>
    <?php
    if (isset($_SESSION['servicereturn'])) {
        echo "<script>";
        $serviceclass = $_SESSION['servicereturn']['class'];
        $servicemsg = $_SESSION['servicereturn']['msg'];
        echo "vue.showMessage('$servicemsg', '$serviceclass', 'bottom-center')";
        unset($_SESSION['servicereturn']);
        echo "</script>";
    }
    ?>
</body>
</html>
