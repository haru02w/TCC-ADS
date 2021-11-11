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
    
    if ($type != "CUSTOMER") {
        header("Location: /");
    }
    
    if (!isset($_GET['ids']) || !isset($_GET['n']) || !isset($_GET['time'])) {
        header("Location: /customermenu/");
        exit();
    }
    $ids = $_GET['ids'];
    $title = $_GET['n'];
    $creationDate = $_GET['time'];

    $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    $rowser = mysqli_fetch_assoc(searchServices($ids, $title, $creationDate, $conn));
    
    if(is_null($rowuser)) {
        expiredReturn();
    }
    
    if(is_null($rowser)) {
        http_response_code(404);
        include("./errors/404.html");
        die();
    }
    
    $id = $rowuser["ID_CUSTOMER"];
    $idcus = $rowser['COD_CUSTOMER'];
    
    if ($id !== $idcus OR $rowser['STATUS'] == 3) {
        header("Location: /customermenu/");
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] == "POST") {
        if(isset($_POST['UPDATE'])) {
            $title = filter_input(INPUT_POST, 'TITLE', FILTER_SANITIZE_STRING);
            $cleantitle = strtolower(preg_replace("/[^a-zA-Z0-9-]/", "-", strtr(utf8_decode(trim($title)), utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"),"aaaaeeiooouuncAAAAEEIOOOUUNC-")));
            $cleantitle = preg_replace('/-+/', '-', $cleantitle);
            $description = filter_input(INPUT_POST, 'DESCRIPTION', FILTER_SANITIZE_STRING);

            if(empty($title) OR empty($description)) {
                $_SESSION['servicereturn'] = array("msg" => "Por favor, preencha os campos de titulo e descrição!","class" => "is-danger");
            }
            else {
                $stmt = mysqli_prepare($conn, "UPDATE TB_SERVICES SET TITLE = ?, CLEANTITLE = ?, DESCRIPTION = ? WHERE ID_SERVICE = ?");
                mysqli_stmt_bind_param($stmt, "ssss", $title, $cleantitle, $description, $ids);
                $bool = mysqli_stmt_execute($stmt);
                mysqli_close($conn);
        
                if($bool) {
                    $_SESSION['servicereturn'] = array("msg" => "As informações do serviço foram alteradas com sucesso!","class" => "is-success");
                }
                else {
                    $_SESSION['servicereturn'] = array("msg" => "Falha ao alterar as informações do serviço! Por favor, tente novamente mais tarde!","class" => "is-danger");
                }
            }
        } 
        if(isset($_POST['DELETE'])){
            $stmt = mysqli_prepare($conn, "DELETE FROM TB_SERVICES WHERE ID_SERVICE = ?");
            mysqli_stmt_bind_param($stmt, "s", $ids);
            $bool = mysqli_stmt_execute($stmt);
    
            if($bool) {
                $_SESSION['servicereturn'] = array("msg" => "O serviço foi removido com sucesso!","class" => "is-success");
            }
            else {
                $_SESSION['servicereturn'] = array("msg" => "Falha ao remover os serviço! Por favor, tente novamente mais tarde!","class" => "is-danger");
            }
        }

        switch($rowser['STATUS']){
            case 0:
                header("Location: /customermenu/");
                break;
            case 1:
                header("Location: /pendingservices/");
                break;
            case 2:
                header("Location: /developmentservices/");
                break;
        }
        exit();
    }
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
                                    <button class="button is-medium is-primary" type="submit" name="UPDATE">Alterar serviço</button>
                                    <button class="button is-medium is-danger" type="submit" name="DELETE">Apagar Serviço</button>
                                    <button class="button is-medium is-info" type="button" @click="onClickCancel">Cancelar alteração</button>
                                </div>
                            </div>
                        </div>           
                    </form>
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
                    this.isActiveBurger = !this.isActiveBurger;
                },
                onClickCancel() {
                    switch(<?php echo $rowser['STATUS']; ?>) {
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
