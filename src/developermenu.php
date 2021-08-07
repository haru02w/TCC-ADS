<?php
    session_start();

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
        <?php require("headerdeveloper.php"); ?>
        
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    
                </div>
            </div>
        </section>
        
    </div>
    <script>
        new Vue({
            el: '#app',
            data: {
                isActiveBurger: false
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


