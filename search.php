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
require('./php/connection.php');
require('./php/functions.php');

if (isset($_COOKIE['EMAIL']) && isset($_COOKIE['TYPE'])) {
    $type = $_COOKIE['TYPE'];
} else if (isset($_SESSION['EMAIL']) && isset($_SESSION['TYPE'])) {
    if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {
        expiredReturn();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
    $type = $_SESSION['TYPE'];
} else {
    expiredReturn();
}

if ($type != "DEVELOPER") {
    header("Location: /");
    exit();
}

if (!isset($_GET['q'])) {
    $q = "%%";
} else {
    $q = "%{$_GET['q']}%";
}

$q = filter_var($q, FILTER_SANITIZE_STRING);

$category = mysqli_query($conn, "SELECT * FROM TB_CATEGORY");

if (empty($_POST['FILTER2'])) {

    $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES WHERE (TITLE LIKE ? OR DESCRIPTION LIKE ?) AND STATUS <= 0 ORDER BY TITLE");
    mysqli_stmt_bind_param($stmt, "ss", $q, $q);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

}else if (isset($_POST['FILTER2'])) {
    $category_select2 = $_POST['category_select'];
    //$time_service = $_POST['time'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES WHERE COD_CATEGORY = ?");
    mysqli_stmt_bind_param($stmt, "s", $category_select2);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
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
        <?php require("./headerdeveloper.php"); ?>
        <br>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <section class="hero is-dark">
                        <div class="hero-body">
                            <p class="title">
                                Resultados da pesquisa
                            </p>
                        </div>
                    </section>
                    <br>
                    <div class="container">
                        <div class="select">
                            <select>
                                <option name="time" value="recent">Mais recentes</option>
                                <option name="time" value="old">Mais antigos</option>
                            </select>
                        </div>
                        <div class="select">
                            <select>
                                <?php while ($result_category = mysqli_fetch_assoc($category)) { ?>
                                    <option name="category_select" value="<?php echo $result_category['ID_CATEGORY'] ?>"><?php echo $result_category['NAME'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <button class="button is-success" type="submit" name="FILTER2">Aplicar Filtros</button>
                    </div>
                    <div class="section is-fullheight">
                        <?php if ($result->num_rows <= 0) { ?>
                            <div class="box">
                                <p class="title is-5"> Nenhum serviço foi encontrado! </p>
                            </div>
                        <?php } ?>
                        <div class="columns is-variable is-multiline">
                            <?php while ($rowser = mysqli_fetch_assoc($result)) { ?>
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
                                            <a href="/details/<?php echo $rowser['ID_SERVICE']; ?>" class="card-footer-item">Ver detalhes</a>
                                        </footer>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
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
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger
                },
            }
        })
    </script>
</body>

</html>
