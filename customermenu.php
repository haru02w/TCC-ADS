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
    require("./php/connection.php");
    require("./php/functions.php");
    
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

    if($type != "CUSTOMER") {
        header("Location: /");
        exit();
    }

    $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    
    if(is_null($rowuser)) {
        expiredReturn();
    }
    
    $id = $rowuser["ID_CUSTOMER"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES WHERE COD_CUSTOMER = ? AND STATUS = 0");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultserv = mysqli_stmt_get_result($stmt);
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hatchfy</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
    <script src="/js/vue.js"></script>
    <script src="/js/jscript.js"></script>
    <script src="/js/v-mask.min.js"></script>
    <script src="/js/moment.js"></script>
    <script src="/js/bulma-toast.min.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php require("./headercustomer.php");?>
        <br>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <section class="hero is-dark">
                        <div class="hero-body is-dark">
                            <p class="title">
                                Meus serviços
                            </p>
                        </div>
                    </section>
                    <div class="section is-fullheight">   
                        <?php if($resultserv->num_rows <= 0) { ?>
                            <div class="box">
                                <p class="title is-5"> Você não tem serviços sem desenvolvedores! <a href="/createservice/" class="is-5">Clique aqui</a> para criar um serviço!</p>
                            </div>
                        <?php } ?>
                        <div class="columns is-variable is-multiline">
                        <?php while ($rowser = mysqli_fetch_assoc($resultserv)) { ?>
                            <div class="column is-4">
                                <div class="card bm--card-equal-height">
                                    <header class="card-header">
                                        <p class="card-header-title"><?php echo $rowser['TITLE']; ?></p>
                                    </header>
                                    <div class="card-content">
                                        <div class="content">
                                            <?php echo $rowser['DESCRIPTION']; ?> 
                                        </div>
                                    </div>
                                    <footer class="card-footer">
                                        <a href="/details/<?php echo $rowser['ID_SERVICE'];?>" class="card-footer-item">Ver detalhes</a>
                                        <a href="/updateservice/<?php echo $rowser['ID_SERVICE'];?>" class="card-footer-item">Editar serviço</a>
                                    </footer>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php require "baseboard.php"?>
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
            }
        })
    </script>
    <?php if (isset($_SESSION['servicemsg'])) {
    echo "<script>";
            $serviceclass = $_SESSION['serviceclass'];
            $servicemsg = $_SESSION['servicemsg'];
            echo "bulmaToast.toast({ message: '$servicemsg', type: '$serviceclass', duration: 6000, position: 'bottom-center', dismissible: true, pauseOnHover: true, closeOnClick: false, animate: { in: 'fadeIn', out: 'fadeOut' }, })";
            unset($_SESSION['servicemsg']);
            unset($_SESSION['serviceclass']);
    echo "</script>";
    } ?>
</body>
</html>
