<?php
    session_name("HATIDS");
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    require("connection.php");
    require("functions.php");

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
    
    if ($type != "CUSTOMER") {
        header("Location: /");
    }
    
    if (!isset($_GET['ids'])) {
        header("Location: /customermenu/");
        exit();
    }
    $ids = $_GET['ids'];

    $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    if(is_null($rowuser)) {
        expiredReturn();
    }
    $id = $rowuser["ID_CUSTOMER"];

    $rowser = mysqli_fetch_assoc(searchServices($ids, $conn));
    $idcus = $rowser['COD_CUSTOMER'];
    if ($id !== $idcus OR $row['STATUS'] == 3) {
        header("Location: /customermenu/");
        exit();
    }

    if(isset($_POST['TITLE']) AND isset($_POST['CONTACT']) AND isset($_POST['DESCRIPTION'])) {
        $title = filter_input(INPUT_POST, 'TITLE', FILTER_SANITIZE_STRING);
        $contact = filter_input(INPUT_POST, 'CONTACT', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'DESCRIPTION', FILTER_SANITIZE_STRING);

        $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET TITLE = ?, DESCRIPTION = ?, CONTACT = ? WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $contact, $ids);
        $bool = mysqli_stmt_execute($stmt);
        mysqli_close($conn);

        if($bool) {
            $update = "success";
        }
        else {
            $update = "failure";
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hatchfy</title>
    <link rel="stylesheet" href="https://hatchfy.philadelpho.tk/css/style.css">
    <script src="https://hatchfy.philadelpho.tk/js/vue.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/jscript.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/v-mask.min.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/moment.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php require("headercustomer.php"); ?>
        <br>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <section class="hero is-dark">
                        <div class="hero-body is-dark">
                            <p class="title">
                                Editar serviço
                            </p>
                        </div>
                    </section>
                    <form action="" method="POST">
                        <div class="section">
                            <div class="columns">
                                <div class="column is-5">
                                    <div class="field">
                                        <label class="label">Título do serviço</label>
                                        <input class="input" type="text" name="TITLE" placeholder="Digite o título do serviço" value="<?php echo $rowser['TITLE'];?>">
                                    </div>
                                    <div class="control">
                                        <label class="label" for="description">Descrição do serviço</label>
                                        <textarea class="textarea has-fixed-size" placeholder="Digite a descrição do serviço" name="DESCRIPTION"> <?php echo $rowser['DESCRIPTION'];?> </textarea>
                                    </div>
                                </div>
                                <div class="column is-5">
                                    <div class="field">
                                        <label class="label" for="contact">Contato</label>
                                        <input class="input" type="tel" name="CONTACT" value="<?php echo $rowser['CONTACT'];?>" placeholder="Digite aqui o seu contato">
                                    </div>
                                </div>
                            </div>
                            <div class="section has-text-centered">
                                <div class="field">
                                    <button class="button is-medium is-primary" type="submit"> Alterar serviço </button>
                                    <button class="button is-medium is-danger" type="button" @click="onClickCancel"> Cancelar alteração </button>
                                </div>
                            </div>
                        </div>           
                    </form>
                </div>
            </div>
        </section>
        <div class="modal" :class="topModalReturn">
            <div class="modal-background"></div>
            <div class="modal-content">
                <div class="box">
                    <article class="message" :class="messageModalReturn">
                        <div class="message-header">
                            <p v-if="isActiveReturn == 'success'">Sucesso</p>
                            <p v-else if="isActiveReturn == 'failure'">Falha</p>
                            <button class="delete" aria-label="close" @click="onClickButtonReturn" v-if="isActiveReturn == 'success' || isActiveReturn == 'failure'"></button>
                        </div>
                        <div v-if="isActiveReturn == 'success'" class="message-body">
                            O serviço foi alterado com sucesso!
                        </div>
                        <div v-else-if="isActiveReturn == 'failure'" class="message-body">
                            Falha ao alterar o serviço! Por favor, tente novamente mais tarde!
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
    <noscript> <style> .script {display:none;}</style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
    <script>
        new Vue({
            el: '#app',
            data: {
                isActiveBurger: false,
                isActiveReturn: "<?php if(isset($update)) { echo $update; }?>",
            },
            computed: {
                topModalReturn : function () {
                    return {
                        'is-active': this.isActiveReturn == 'success' || this.isActiveReturn == 'failure'
                    }
                },
                messageModalReturn : function () {
                    return {
                        'is-success': this.isActiveReturn == 'success',
                        'is-danger':  this.isActiveReturn == 'failure',
                    }
                }
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger
                },
                onClickLogout() {
                    window.location.replace("/logout/")
                },
                onClickCancel() {
                    switch(<?php echo $row['STATUS']; ?>) {
                        case 0:
                            window.location.replace("/customermenu/")
                            break;
                        case 1:
                            window.location.replace("/pendingservices/")
                            break;
                        case 2:
                            window.location.replace("/developmentservices/");
                            break;
                    }
                    
                },
                onClickButtonReturn() {
                    switch(<?php echo $row['STATUS'] ?>) {
                        case 0:
                            window.location.replace("/customermenu/")
                            break;
                        case 1:
                            window.location.replace("/pendingservices/")
                            break;
                        case 2:
                            window.location.replace("/developmentservices/");
                            break;
                    }
                }
            }
        })
    </script>
</body>

</html>
