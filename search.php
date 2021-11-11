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
        $type = $_COOKIE['TYPE'];
    } 
    else if (isset($_SESSION['EMAIL']) && isset($_SESSION['TYPE'])) {
        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {
            expiredReturn();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
        $type = $_SESSION['TYPE'];
    } 
    else {
        expiredReturn();
    }

    if ($type != "DEVELOPER") {
        header("Location: /");
        exit();
    }

    $category = mysqli_query($conn, "SELECT * FROM TB_CATEGORY");

    $timeService = filter_input(INPUT_GET, "t", FILTER_SANITIZE_STRING);
    $categorySelect = filter_input(INPUT_GET, "cs", FILTER_SANITIZE_STRING);
    $actpage = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT);
    $q = filter_input(INPUT_GET, "q", FILTER_SANITIZE_STRING);

    if(empty($q)) {
        $querySearch = "%%";
    }
    else {
        $querySearch = "%{$q}%";
    }

    if(empty($timeService) || empty($categorySelect)) {
        header("Location: https://hatchfy.philadelpho.tk/search/?t=recent&cs=all");
        exit();
    }

    if(empty($actpage)) {
        $actpage = 1;
    }

    $resultsPerPage = 10;
    $startingOffset = ($actpage - 1) * $resultsPerPage;

    if($timeService == "recent") {
        $qTimeParam = "DESC";
    }
    else {
        $qTimeParam = "";
    }

    if($categorySelect == "all") {
        $sql = "SELECT *, SubString(DESCRIPTION,1,180) as DESC2 FROM TB_SERVICES S JOIN TB_CATEGORY C ON (C.ID_CATEGORY = S.COD_CATEGORY) WHERE (TITLE LIKE ? OR DESCRIPTION LIKE ?) AND STATUS = 0 ORDER BY CREATIONDATE $qTimeParam ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $querySearch, $querySearch);
    }
    else if ($categorySelect != "all") {
        $sql = "SELECT *, SubString(DESCRIPTION,1,180) as DESC2 FROM TB_SERVICES S JOIN TB_CATEGORY C ON (C.ID_CATEGORY = S.COD_CATEGORY) WHERE (TITLE LIKE ? OR DESCRIPTION LIKE ?) AND COD_CATEGORY = ? AND STATUS = 0 ORDER BY CREATIONDATE $qTimeParam ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $querySearch, $querySearch, $categorySelect);
    } 
    else {
        header("Location: https://hatchfy.philadelpho.tk/search/?t=recent&cs=all");
        exit();
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $numResults = $result->num_rows;
    $numberPages = ceil($numResults / $resultsPerPage);

    $stmt = mysqli_prepare($conn, $sql."LIMIT ". $startingOffset . ',' . $resultsPerPage);

    if($categorySelect == "all") {
        mysqli_stmt_bind_param($stmt, "ss", $querySearch, $querySearch);
    }
    else {
        mysqli_stmt_bind_param($stmt, "sss", $querySearch, $que, $categorySelect);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

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
                            <p class="title"> Resultados da pesquisa </p>
                        </div>
                    </section>
                    <br>
                    <div class="container">
                        <form id="Filters" action="" method="GET">
                            <div class="field is-horizontal">
                                <div class="field-body">
                                    <div class="field">
                                        <p class="control is-expanded">
                                            <input class="input" type="text" placeholder="Nome ou descrição de um serviço" name="q" value="<?php echo $_GET['q']; ?>">
                                        </p>
                                    </div>
                                    <div class="field is-narrow">
                                        <div class="control">
                                            <div class="select">
                                                <select name="t" onchange="document.getElementById('Filters').submit()">
                                                    <option value="recent" <?php if($_GET['t'] == "recent") {echo "selected";}?>>Mais recentes</option>
                                                    <option value="old" <?php if($_GET['t'] == "old") {echo "selected";}?>>Mais antigos</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field is-narrow">
                                        <div class="control">
                                            <div class="select">
                                                <div class="select">
                                                    <select name="cs" onchange="document.getElementById('Filters').submit()">
                                                        <option value="all" <?php if($_GET['cs'] == "all") { echo "selected";}?>>Todas as categorias</option>
                                                        <?php while ($result_category = mysqli_fetch_assoc($category)) {  ?>
                                                            <option value="<?php echo $result_category['ID_CATEGORY'] ?>" <?php if($result_category['ID_CATEGORY'] == $_GET['cs']) {echo "selected";}?>><?php echo $result_category['NAME'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>                             
                        </form>
                        <br>
                        <?php if($numberPages != 0) { ?>
                            <div class="box is-hidden-desktop">
                                <nav class="pagination is-centered" role="navigation" aria-label="Paginação">
                                    <ul class="pagination-list">
                                        <?php 
                                            for($page = 1; $page <= $numberPages; $page++) { ?>
                                                <li><a href="https://hatchfy.philadelpho.tk/search/?t=<?php echo $timeService .'&cs='. $categorySelect . '&page='. $page . '&q=' . $q ?>"class="pagination-link <?php if($actpage == $page) { echo "is-current";} ?>"><?php echo $page ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php } ?>
                    </div>
                    <br>
                    <?php if ($result->num_rows <= 0) { ?>
                        <div class="box">
                            <p class="title is-5"> Nenhum serviço foi encontrado! </p>
                        </div>
                    <?php } ?>
                    <div class="columns is-variable is-multiline">
                    <?php while ($rowser = mysqli_fetch_assoc($result)) { ?>
                            <div class="column is-6">
                                <div class="card bm--card-equal-height">
                                    <div class="card-content">
                                        <div class="content">
                                            <p class="title is-3">
                                                <?php echo $rowser['TITLE']; ?>
                                            </p>
                                            <p class="subtitle is-5">
                                                <?php echo publishedDate($rowser['CREATIONDATE'])?>
                                            </p>
                                            <p class="subtitle is-4">
                                                <?php echo $rowser['DESC2']; ?> ...
                                            </p>
                                            <a href="https://hatchfy.philadelpho.tk/search/?t=recent&cs=<?php echo $rowser['ID_CATEGORY'];?>" class="button is-rounded is-link"><?php echo $rowser['NAME']?></a>
                                        </div>
                                    </div>
                                    <footer class="card-footer">
                                        <a href="/details/<?php echo $rowser['ID_SERVICE'];?>/<?php echo $rowser['CREATIONDATE']?>/<?php echo $rowser['CLEANTITLE']; ?>" class="card-footer-item">Ver detalhes</a>
                                    </footer>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if($numberPages != 0) { ?>
                        <div class="box">
                            <nav class="pagination is-centered" role="navigation" aria-label="Paginação">
                                <ul class="pagination-list">
                                    <?php 
                                        for($page = 1; $page <= $numberPages; $page++) { ?>
                                            <li><a href="https://hatchfy.philadelpho.tk/search/?t=<?php echo $timeService .'&cs='. $categorySelect . '&page='. $page . '&q=' . $q ?>"class="pagination-link <?php if($actpage == $page) { echo "is-current";} ?>"><?php echo $page ?></a></li>
                                    <?php } ?>
                                </ul>
                            </nav>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
            <?php require "baseboard.php" ?>
    </div>
    <noscript> <style> .script { display: none; } </style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
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
            }
        })
    </script>
</body>
</html>
