<?php
    session_name("HATIDS");
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    require("./connection.php");
    require("./functions.php");

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
        header("Location: ../");
    }
    
    if (!isset($_GET['ids'])) {
        header("Location: ../customermenu/");
        exit();
    }
    $ids = $_GET['ids'];

    $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    $rowser = mysqli_fetch_assoc(searchServices($ids, $conn));
    
    if(is_null($rowuser)) {
        expiredReturn();
    }
    
    if(is_null($rowser)) {
        header("Location: ../customermenu/");
        exit();
    }
    
    $id = $rowuser["ID_CUSTOMER"];
    $idcus = $rowser['COD_CUSTOMER'];
    
    if ($id !== $idcus OR $rowser['STATUS'] == 3) {
        header("Location: ../customermenu/");
        exit();
    }

    if(isset($_POST['TITLE']) && isset($_POST['DESCRIPTION'])) {
        $title = filter_input(INPUT_POST, 'TITLE', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'DESCRIPTION', FILTER_SANITIZE_STRING);

        $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET TITLE = ?, DESCRIPTION = ? WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $ids);
        $bool = mysqli_stmt_execute($stmt);
        mysqli_close($conn);

        if($bool) {
            $_SESSION['update'] = "As informações do serviço foram alteradas com sucesso!";
            $_SESSION['updateclass'] = "is-success";
        }
        else {
            $_SESSION['update'] = "Falha ao alterar as informações do serviço! Por favor, tente novamente mais tarde!";
            $_SESSION['updateclass'] = "is-danger";
        }
    }
    
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hatchfy</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
    <script src="../js/vue.js"></script>
    <script src="../js/jscript.js"></script>
    <script src="../js/v-mask.min.js"></script>
    <script src="../js/moment.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php require("./headercustomer.php"); ?>
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
                            <?php if (isset($_SESSION['update']) AND $_SESSION['update'] != "") { ?>
                                <div class="notification <?php echo $_SESSION['updateclass']; ?>">
                                    <?php echo $_SESSION['update']; ?>                          
                                </div>
                            <?php } ?>
                            <?php unset($_SESSION['update']); unset($_SESSION['updateclass']);?> 
                            <div class="columns">
                                <div class="column is-5">
                                    <div class="field">
                                        <label class="label">Título do serviço</label>
                                        <input class="input" type="text" name="TITLE" placeholder="Digite o título do serviço" value="<?php echo $rowser['TITLE'];?>">
                                    </div>
                                    <div class="control">
                                        <label class="label" for="description">Descrição do serviço</label>
                                        <textarea class="textarea has-fixed-size" placeholder="Digite a descrição do serviço" name="DESCRIPTION"><?php echo $rowser['DESCRIPTION'];?></textarea>
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
    </div>
    <noscript> <style> .script {display:none;}</style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
    <script>
        new Vue({
            el: '#app',
            data: {
                isActiveBurger: false,
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger
                },
                onClickLogout() {
                    window.location.replace("../logout/")
                },
                onClickCancel() {
                    switch(<?php echo $rowser['STATUS']; ?>) {
                        case 0:
                            window.location.replace("../customermenu/")
                            break;
                        case 1:
                            window.location.replace("../pendingservices/")
                            break;
                        case 2:
                            window.location.replace("../developmentservices/");
                            break;
                    }
                    
                },
            }
        })
    </script>
</body>
</html>
