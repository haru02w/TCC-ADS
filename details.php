<?php
    session_name("HATIDS");
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    require('connection.php');
    require('functions.php');

    if(isset($_COOKIE['EMAIL']) && isset($_COOKIE['TYPE'])) {
        $email = $_COOKIE['EMAIL'];
        $type = $_COOKIE['TYPE'];
    }
    else if(isset($_SESSION['EMAIL']) && isset($_SESSION['TYPE'])) {
        if(isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {
            expiredReturn();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
        $email = $_SESSION['EMAIL'];
        $type = $_SESSION['TYPE'];
    }
    else {
        expiredReturn();
    }

    if (!isset($_GET['ids'])) {
        header("Location: /".strtolower($type)."menu.php");
        exit();
    }
    $ids = $_GET['ids'];
    
    $result = searchServices($ids, $conn);
    if ($result->num_rows <= 0) {
        header("Location: /".strtolower($type)."menu.php");
        exit();
    }

    $row = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    
    if(is_null($row)) {
        expiredReturn();
    }
    
    $id = $row["ID_$type"];

    $iddev = $row['COD_DEVELOPER'];
    $idcus = $row['COD_CUSTOMER'];

    $infodev = searchInfoDev($iddev, $conn);
    $infocus = searchInfoCus($idcus, $conn);

    if($row['STATUS'] >= 1) {
        $birthdev = explode("-", $infodev['BIRTH_DATE']);
        $infodev['BIRTH_DATE'] = $birthdev[2] . "/" . $birthdev[1] . "/" . $birthdev[0];
    }

    $birthcus = explode("-", $infocus['BIRTH_DATE']);
    $infocus['BIRTH_DATE'] = $birthcus[2] . "/" . $birthcus[1] . "/" . $birthcus[0];

    if (isset($_POST['REQUEST'])) {
        $status = searchServices($ids, $conn); $status = $status['STATUS'];

        if ($status >= 1) {
            $_SESSION['detail'] = "takend";
            header("Location: /pendingservices.php");
            exit();
        }
        else {
            $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET COD_DEVELOPER = ?, STATUS = 1 WHERE ID_SERVICE = ?");
            mysqli_stmt_bind_param($stmt, "ss", $id, $ids);
            $bool = mysqli_stmt_execute($stmt);

            if ($bool) {
                $_SESSION['detail'] = "successd";
                header("Location: /pendingservices.php");
                exit();
            } 
            else {
                $_SESSION['detail'] = "failured";
                header("Location: /pendingservices.php");
                exit();
            }
        }
    } 
    elseif (isset($_POST['SEND'])) {
        $stmt2 = mysqli_prepare($conn, "UPDATE TB_SERVICES SET STATUS = 2 WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt2, "s", $ids);
        $bool = mysqli_stmt_execute($stmt2);

        if ($bool) {
            $_SESSION['send'] = "successs";
            header("Location: /developmentservices.php");
            exit();
        } 
        else {
            $_SESSION['send'] = "failures";
            header("Location: /developmentservices.php");
            exit();
        }
    } 
    elseif (isset($_POST['SENDRECUSE'])) {
        $stmt3 = mysqli_prepare($conn, "UPDATE TB_SERVICES SET COD_DEVELOPER = NULL, STATUS = 0 WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt3, "s", $ids);
        $bool = mysqli_stmt_execute($stmt3);

        if ($bool) {
            $_SESSION['recuse'] = "successre";
            header("Location: /pendingservices.php");
            exit();
        } 
        else {
            $_SESSION['recuse'] = "failurere";
            header("Location: /pendingservices.php");
            exit();
        }
    }

    if ($type == "CUSTOMER") {
        if ($idcus !== $id) {
            header("Location: /customermenu.php");
            exit();
        }
    } 
    elseif ($type == "DEVELOPER" AND $row['STATUS'] >= 1) {
        if ($iddev !== $id) {
            header("Location: /developermenu.php");
            exit();
        }
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hatchfy</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/vue.js"></script>
    <script src="js/jscript.js"></script>
    <script src="js/v-mask.min.js"></script>
    <script src="js/moment.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php if ($type == "CUSTOMER") {
            require("headercustomer.php");
        } else {
            require("headerdeveloper.php");
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
                                            <p class="subtitle is-5"><?php echo $row['TITLE']; ?></p>
                                        </div>
                                    </div>
                                    <div class="control">
                                        <label class="label" for="description">Descrição do serviço</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $row['DESCRIPTION']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-5">
                                    <div class="field">
                                        <label class="label" for="contact">Contato</label>
                                        <div class="box">
                                            <p class="subtitle is-5"><?php echo $row['CONTACT']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                <?php if($row['STATUS'] >= 1) { ?>
                                <div class="columns">
                                    <div class="column is-3">
                                        <div class="field">
                                            <label class="label has-text-centered"> Foto do desenvolvedor </label>
                                            <figure class="image is-square">
                                                <img class="is-rounded" src="<?php echo $infodev['IMAGE']?>">
                                            </figure>
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
                                <?php if($row['STATUS'] <= 0) { ?>
                                    <div class="box">
                                        <p class="title is-5"> Ainda não há um desenvolvedor! </p>
                                    </div>
                                <?php } ?>
                                <?php if ($row['STATUS'] == 1) { ?>
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
                                                <img class="is-rounded" src='<?php echo $infocus['IMAGE']; ?>'>
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
                                    </div>
                                </div>
                                <?php if ($row['STATUS'] == 0) { ?>
                                    <div class="section has-text-centered">
                                        <div class="field">
                                            <button class="button is-medium is-primary" name="REQUEST" type="submit"> Enviar solicitação </button>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($row['STATUS'] == 1) { ?>
                                    <div class="section has-text-centered">                                        
                                        <div class="notification is-primary">
                                            <p class="title is-5"> Aguardando a confirmação pelo cliente...</p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </section>


    </div>
    <noscript> <style> .script {display:none;}</style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
    <script>
        new Vue({
            el: '#app',
            data: {
                isActiveBurger: false
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger
                },
                onClickLogout() {
                    window.location.replace("logout.php")
                }
            }
        })
    </script>
</body>

</html>