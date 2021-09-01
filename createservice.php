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
    require('./connection.php');
    require('./functions.php');

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

    if ($type == "DEVELOPER") {
        header("Location: ./developermenu/");
        exit();
    }
$rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
$id = $rowuser["ID_$type"];
$result_category = "SELECT * FROM TB_CATEGORY";
$result_cat = mysqli_query($conn, $result_category);


$status = 0;
$id_developer = null;

if (isset($_POST['CREATE'])){
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $category  = $_POST['category_service'];
    $stmt = mysqli_prepare($conn, "INSERT INTO TB_SERVICES (COD_CUSTOMER, COD_DEVELOPER, COD_CATEGORY, TITLE, DESCRIPTION, STATUS) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssssss", $id, $id_developer,  $category, $title, $desc, $status);
    mysqli_stmt_execute($stmt);
    unset($_POST);
}
mysqli_close($conn);
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
                                Crie seu Serviço
                            </p>
                        </div>
                    </section>
                    <form action="" method="POST">
                        <div class="section">
                            <div class="columns is-centered">
                                <div class="column is-5">
                                    <div class="field">
                                        <label class="label">Título do serviço</label>
                                        <input class="input" type="text" name="title" placeholder="Digite o título do serviço">
                                    </div>
                                    <div class="control">
                                        <label class="label" for="description">Descrição do serviço</label>
                                        <textarea class="textarea has-fixed-size" placeholder="Digite a descrição do serviço" name="description"></textarea>
                                    </div>
                                </div>
                                <div class="column is-5">
                                    <div class="field">
                                        <label class="label" for="category">Tipo de serviço</label>
                                        <div class="select">
                                            <select name="category_service">
                                                <option disabled selected>Selecione o tipo do serviço</option>
                                                <?php while($row_category = mysqli_fetch_assoc($result_cat)){?><option name="category" value="<?php echo $row_category['ID_CATEGORY'];?>"><?php echo $row_category['NAME'];?></option> <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="field is-grouped is-grouped-centered is-grouped-multiline">
                                <p class="control">
                                    <button type="submit" name="CREATE" class="button is-primary">Criar</button>
                                </p>
                                <p class="control">
                                    <button type="button" class="button is-danger" onclick="window.location.replace('../customermenu/')">Cancelar criação</button>
                                </p>
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
                }
            }
        })
    </script>
</body>
</html>
