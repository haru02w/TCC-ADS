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
    
    if ($_SESSION['TYPE'] == "DEVELOPER") {

        header("Location: /developermenu.php");
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
        <?php require("headercustomer.php"); ?>
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
                    <div class="section">
                        <form action="" method="POST">
                            <div class="columns">
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
                                        <label class="label" for="contact">Contato</label>
                                        <input class="input" type="tel" name="contact" placeholder="Digite aqui o seu contato">
                                    </div>
                                    <div class="field">
                                        <label class="label" for="category">Tipo de serviço</label>
                                        <div class="select">
                                            <select name="category">
                                                <option disabled selected>Selecione o tipo do serviço</option>
                                                <option>Padaria</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="button is-primary">Criar</button>
                                </div>
                            </div>
                        </form>
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