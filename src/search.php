<?php
    session_start();
    require('connection.php');

    if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['PASSWORD']) || !isset($_SESSION['TYPE']) || isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {

        session_unset();
        $_SESSION['s'] = "expired";
        header("Location: /");
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time();

    if ($_SESSION['TYPE'] == "CUSTOMER") {

        header("Location: /customermenu.php");
        exit();
    }

    if (!isset($_GET['q'])) {
        $q = "%%";
    }
    else {
        $q = "%{$_GET['q']}%";
    }

    $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES WHERE (TITLE LIKE ? OR DESCRIPTION LIKE ?) AND STATUS <= 0 ORDER BY TITLE");
    mysqli_stmt_bind_param($stmt, "ss", $q, $q);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
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
    <div id="app">
        <?php require("headerdeveloper.php");?>
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
                    <div class="section is-fullheight">
                        <?php if($result->num_rows <= 0) { ?>
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
                                        <a href="details.php?ids=<?php echo $rowser['ID_SERVICE']; ?>" class="card-footer-item">Ver detalhes</a>
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
                    window.location.replace("logout.php")
                }
            }
        })
    </script>
</body>

</html>

