<?php
    date_default_timezone_set('America/Sao_Paulo');

    if (!isset($_GET['selector']) || !isset($_GET['validator'])) {
        header("Location: /index.php");
        exit();
    }

    $selector = $_GET['selector'];
    $validator = $_GET['validator'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir a senha</title>
    <link rel="stylesheet" href="https://hatchfy.philadelpho.tk/css/style.css">
    <script src="https://hatchfy.philadelpho.tk/js/vue.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/zxcvbn.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <div class="columns is-centered">
                        <div class="column is-6 is-vcentered">
                            <div class="box has-text-centered">
                                <?php
                                if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) {
                                ?>
                                    <form action="/submitpwdreset/" method="post">
                                        <input type="hidden" name="selector" value="<?php echo $selector?>">
                                        <input type="hidden" name="validator" value="<?php echo $validator?>">
                                        <div class="field">
                                            <label for="PASSWORD1" class="label">Nova senha</label>
                                            <div class="control has-icons-left">
                                                <input type="password" class="input" :class="{'is-primary': validatePassword() == 4, 'is-success': validatePassword() == 3, 'is-warning': validatePassword() == 2, 'is-danger': validatePassword() <= 1}" placeholder="Digite a sua nova senha" v-model="passwd1" name="PASSWORD1" @input="validSubmit()" required>
                                                <span class="icon is-small is-left">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                            </div>
                                            <p class="help is-primary" v-show="validatePassword() == 4">Excelente</p>
                                            <p class="help is-success" v-show="validatePassword() == 3">Forte</p>
                                            <p class="help is-warning" v-show="validatePassword() == 2">Médio</p>
                                            <p class="help is-danger" v-show="validatePassword() == 1">Fraca. Insira uma senha mais forte!</p>
                                            <p class="help is-danger" v-show="validatePassword() == 0">Muito fraca. Insira uma senha mais forte!</p>
                                        </div>
                                        <div class="field">
                                            <label for="PASSWORD2" class="label">Confirmar a senha</label>
                                            <div class="control has-icons-left has-icons-right">
                                                <input type="password" class="input" :class="{'is-danger': confirmPassword() == false}" placeholder="Confirme a sua senha" v-model="passwd2" name="PASSWORD2" @input="validSubmit()" required>
                                                <span class="icon is-small is-left">
                                                    <i class="fa fa-lock"></i>
                                                </span>
                                                <span class="icon is-small is-right">
                                                    <i class="fas" :class="{'fa-exclamation-triangle' : confirmPassword() == false}"></i>
                                                </span>
                                            </div>
                                            <p class="help is-danger" v-show="confirmPassword() == false">As senhas não correspondem!</p>
                                        </div>
                                        <button class="button is-info" type="submit" name="reset-password-submit" v-bind:disabled="!casePass"> Redefinir senha</button>
                                    </form>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                isConfirmPassword: false,
                isValidPassword: false,
                casePass: false,
                passwd1: "",
                passwd2: "",
            },
            methods: {
                validSubmit() {
                    this.isConfirmPassword = this.confirmPassword();
                    this.isValidPassword = this.validatePassword();

                    if (this.isValidPassword >= 4 && this.isConfirmPassword == true) {
                        this.casePass = true;
                    } else {
                        this.casePass = false;
                    }
                },
                validatePassword() {
                    if (this.passwd1 != "") {
                        resultado = zxcvbn(this.passwd1);
                        return resultado.score;
                    }
                },
                confirmPassword() {
                    if (this.passwd1 != "" && this.passwd2 != "") {
                        if (this.passwd1 != this.passwd2) {
                            return false;
                        }
                        return true;
                    }
                },
            }
        })
    </script>
</body>

</html>