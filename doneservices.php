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

    $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    if(is_null($rowuser)) {
        expiredReturn();
    }
    $id = $rowuser["ID_$type"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES WHERE COD_$type = ? AND STATUS = 3");
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
    <link rel="stylesheet" href="https://hatchfy.philadelpho.tk/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
    <script src="https://hatchfy.philadelpho.tk/js/vue.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/jscript.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/v-mask.min.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/moment.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php if($type == "CUSTOMER") { require("headercustomer.php");} else { require("headerdeveloper.php"); } ?>
        <br>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <section class="hero is-dark">
                        <div class="hero-body is-dark">
                            <p class="title">
                                Serviços concluídos
                            </p>
                        </div>
                    </section>
                    <div class="section is-fullheight">
                        <?php if($resultserv->num_rows <= 0) { ?>
                            <div class="box">
                                <p class="title is-5"> Você não tem serviços concluídos! </p>
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
                                        <a href="/details/<?php echo $rowser['ID_SERVICE'];?>/" class="card-footer-item">Ver detalhes</a>
                                    </footer>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
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
                    window.location.replace("/logout/")
                }
            }
        })
    </script>
</body>

</html>