<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir a senha</title>
    <link rel="stylesheet" href="https://hatchfy.philadelpho.tk/css/style.css">
    <script src="https://hatchfy.philadelpho.tk/js/vue.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <div class="columns is-centered">
                        <div class="column is-6 is-vcentered">
                            <div class="box has-text-centered">
                                <form action="/requestpwdreset/" method="post">
                                    <h3 class="title is-1 has-text-centered has-text-dark">Redefina a sua senha</h3>
                                    <div class="field">
                                        <label class="label has-text-left">Email</label>
                                        <div class="control has-icons-left">
                                            <input type="text" class="input" placeholder="Insira o seu endereço de email" name="EMAILPWD_RESET" required v-model="Email" @input="validSubmit()">
                                            <span class="icon is-small is-left">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <button class="button is-info" type="submit" name="reset-request-submit" v-bind:disabled="!casePass"> Enviar email</button>
                                </form>
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
                Email: "",
                isValidEmail: false,
                casePass: false,
            },
            methods: {
                validateEmail() {
                    var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                    if (this.Email != "") {
                        if (this.Email.match(mailformat)) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                },
                validSubmit() {
                    this.isValidEmail = this.validateEmail();

                    if (this.isValidEmail == true) {
                        this.casePass = true;
                    } else {
                        this.casePass = false;
                    }
                }
            }
        })
    </script>
</body>

</html>