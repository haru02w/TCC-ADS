<?php
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
    require("./php/functionschat.php");
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
    $setoken = $_GET["setoken"];

    $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    $rowser = mysqli_fetch_assoc(searchServiceByToken($conn, $setoken));

    if(is_null($rowuser)) {
        expiredReturn();
    }
    else if(is_null($rowser)) {
        header("Location: /");
        exit();
    }

    if($rowser["STATUS"] <= 1) {
        header("Location: /");
        exit();
    }
    
    $id = $rowuser["ID_$type"];
    $ustoken = $rowuser["TOKENCHAT"];
    $idcus = $rowser["COD_CUSTOMER"];
    $iddev = $rowser["COD_DEVELOPER"];

    if($type == "DEVELOPER") {
        $tochatuser = searchInfoCusByService($idcus, $conn);
    }
    else {
        $tochatuser = searchInfoDevByService($iddev, $conn);
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM TB_CHATMESSAGES WHERE SERVICE = ?");
    mysqli_stmt_bind_param($stmt, "s", $setoken);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

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
    <script src="/js/moment.js"></script>
    <script src="/js/chat.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <?php if($rowser["STATUS"] == 2) { ?>
            <div id="pageloader" class="pageloader is-warning is-right-to-left is-active">
                <p class="title">Conectando no servidor de chat</p>
            </div>
        <?php } ?>
        <?php if ($type == "CUSTOMER") {
            require("./headercustomer.php");
        } else {
            require("./headerdeveloper.php");
        } ?>
        <section class="section is-unselectable">
            <div class="container">
                <div class="box">
                    <article class="media">
                        <figure class="media-left">
                            <p class="image is-64x64 is-square">
                                <img class="is-rounded" style="object-fit: cover;" src="../<?php echo $tochatuser["IMAGE"];?>">
                            </p>
                        </figure>
                        <div class="media-content">
                            <div class="content">
                                <p class="title has-text-left is-size-4"> <?php echo $tochatuser["NAME"]; ?></p>
                                <p class="subtitle has-text-left is-size-5"> (<?php echo $rowser["TITLE"]; ?>)</p>
                            </div>
                            <nav class="level is-mobile"></nav>
                        </div>
                    </article>
                    <br>
                    <div id="ChatBox" class="box">  
                        <?php
                        while($messages = mysqli_fetch_assoc($result)) {
                            if($messages["SENDER"] == $id) {
                                echo '
                                <br>
                                <div class="card has-text-right" style="background: rgba(123, 63, 212)">
                                    <div class="card-content">
                                        <div class="content">
                                            <p class="subtitle is-5 has-text-white">
                                                '.$messages["MESSAGE"].'
                                            </p>
                                            <p class="subtitle is-6 has-text-white">
                                                '.date("d/m/Y H:i", $messages["POSTDATE"]).'
                                            </p>
                                        </div>
                                    </div>
                                </div>';
                            }
                            else {
                                echo '
                                <br>
                                <div class="card has-text-left">
                                    <div class="card-content">
                                        <div class="content">
                                            <p class="subtitle is-5">
                                                '.$messages["MESSAGE"].'
                                            </p>
                                            <p class="subtitle is-6">
                                                '.date("d/m/Y H:i", $messages["POSTDATE"]).'
                                            </p>
                                        </div>
                                    </div>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                    <div class="field has-addons">
                        <div class="control is-expanded has-icons-left">
                            <input class="input is-medium is-rounded" type="text" placeholder="Mensagem" id="ChatRoomSendInput" <?php echo ($rowser["STATUS"] == 3) ? "disabled" : "" ?>/>
                            <span class="icon is-medium is-left">
                                <i class="fas fa-keyboard"></i>
                            </span>
                        </div>
                        <div class="control">
                            <button type="button" id="ChatRoomSendBtn" class="button is-medium is-primary is-rounded" <?php echo ($rowser["STATUS"] == 3) ? "disabled" : "" ?>>
                                <span class="icon is-medium">
                                    <i class="fas fa-paper-plane"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php require "baseboard.php"?>
    </div>
    <noscript> <style> .script {display:none;}</style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
    <script>
    var vue = new Vue({
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
    <?php if($rowser["STATUS"] == 2) { ?>
        <script> 
            let ustoken = "<?php echo $ustoken;?>" ;
            let t = "<?php echo $type == "DEVELOPER" ? "d" : "c"; ?>";
            let setoken = "<?php echo $setoken; ?>";
        </script>
        <script src="/js/chat.js"></script>
    <?php } ?>
</body>
</html>
