<?php
    session_start();
    require("connection.php");
    require("functions.php");

    if (!isset($_SESSION['EMAIL']) || !isset($_SESSION['PASSWORD']) || !isset($_SESSION['TYPE']) || isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {

        session_unset();
        $_SESSION['s'] = "expired";
        header("Location: /");
        exit();
    }
    $_SESSION['LAST_ACTIVITY'] = time();

    if($type == "DEVELOPER") {
        header("Location: /developermenu.php");
        exit();
    }

    $email = $_SESSION['EMAIL'];
    $type = $_SESSION['TYPE'];

    $row = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    $id = $row["ID_CUSTOMER"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES WHERE COD_CUSTOMER = ? AND STATUS = 0");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $resultserv = mysqli_stmt_get_result($stmt);
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
        <?php require("headercustomer.php");?>
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
                                <p class="title is-5"> Você não tem serviços sem desenvolvedores! </p>
                            </div>
                        <?php } ?>
                        <div class="columns is-variable is-multiline">
                        <?php while ($row = mysqli_fetch_assoc($resultserv)) { ?>
                            <div class="column is-4">
                                <div class="card bm--card-equal-height">
                                    <header class="card-header">
                                        <p class="card-header-title"><?php echo $row['TITLE']; ?></p>
                                    </header>
                                    <div class="card-content">
                                        <div class="content">
                                            <?php echo $row['DESCRIPTION']; ?> 
                                        </div>
                                    </div>
                                    <footer class="card-footer">
                                        <a href="details.php?ids=<?php echo $row['ID_SERVICE']; ?>" class="card-footer-item">Ver detalhes</a>
                                        <a href="updateservice.php?ids=<?php echo $row['ID_SERVICE']; ?>" class="card-footer-item">Editar serviço</a>
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
                },
            }
        })
    </script>
</body>

</html>