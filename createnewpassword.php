<?php
date_default_timezone_set('America/Sao_Paulo');
require("connection.php");
require("functions.php");

if (!isset($_GET['selector']) || !isset($_GET['validator'])) {
    header("Location: /");
    exit();
}

$selector = $_GET['selector'];
$validator = $_GET['validator'];

$cdate = date("U");

$stmt = mysqli_prepare($conn, "SELECT * FROM TB_PASSWORDRESET WHERE SELECTOR = ? AND EXPIRES >= ?");
mysqli_stmt_bind_param($stmt, "si", $selector, $cdate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

$tokenb = hex2bin($validator);
$istoken = password_verify($tokenb, $row['TOKEN']);

if (isset($_POST["reset-password-submit"])) {

    $selector = filter_input(INPUT_POST, "selector", FILTER_SANITIZE_STRING);
    $validator = filter_input(INPUT_POST, "validator", FILTER_SANITIZE_STRING);
    $password1 = filter_input(INPUT_POST, "PASSWORD1", FILTER_SANITIZE_STRING);
    $password2 = filter_input(INPUT_POST, "PASSWORD2", FILTER_SANITIZE_STRING);

    if (empty($password1) || empty($password2)) {
        $resetpwdclass = "is-danger";
        $resetpwd = "Preencha os dois campos!";
    } else if ($password1 != $password2) {
        $resetpwdclass = "is-danger";
        $resetpwd = "As senhas não conferem!";
    } else if (empty($selector) || empty($validator)) {
        $istoken = false;
        $row = null;
    } else {
        $tokenEmail = $row['EMAIL'];

        $result1 = searchEmailType($tokenEmail, "DEVELOPER", $conn);
        $result2 = searchEmailType($tokenEmail, "CUSTOMER", $conn);

        if (is_null(mysqli_fetch_assoc($result1)) && is_null(mysqli_fetch_assoc($result2))) {
            $resetpwdclass = "is-danger";
            $resetpwd = "Usuário não encontrado! Por favor, tente novamente mais tarde!";
        } else {
            if ($result1->num_rows >= 1) {
                $type = "DEVELOPER";
            } else if ($result2->num_rows >= 1) {
                $type = "CUSTOMER";
            }
            
            $options = ['cost' => 12];
            $newpwdhash = password_hash($password1, PASSWORD_DEFAULT, $options);
            $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET PASSWORD = ? WHERE EMAIL = ?");
            mysqli_stmt_bind_param($stmt, "ss", $newpwdhash, $tokenEmail);
            $bool = mysqli_stmt_execute($stmt);

            $stmt = mysqli_prepare($conn, "DELETE FROM TB_PASSWORDRESET WHERE EMAIL = ?");
            mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
            $bool2 = mysqli_stmt_execute($stmt);

            session_name("HATIDS");
            session_start();

            if ($bool === true && $bool2 === true) {
                $_SESSION['resetpwd'] = "A sua senha foi alterada com sucesso!";
                $_SESSION['resetpwdclass'] = 'is-success';
                header("Location: /resetpassword/");
                exit();
            } else {
                $_SESSION['resetpwd'] = "A sua senha não foi alterada! Por favor, tente novamente mais tarde!";
                $_SESSION['resetpwdclass'] = 'is-danger';
                header("Location: /resetpassword/");
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redefinir a senha</title>
    <link rel="stylesheet" href="https://hatchfy.philadelpho.tk/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
    <script src="https://hatchfy.philadelpho.tk/js/vue.js"></script>
    <script src="https://hatchfy.philadelpho.tk/js/zxcvbn.js"></script>
</head>

<body class="background">
    <div id="app" class="script">
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <div class="columns is-centered">
                        <div class="column is-7 is-vcentered">
                            <div class="box has-text-centered">
                                <?php if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) { ?>
                                    <?php if (is_null($row) || $istoken == false) { ?>
                                        <article class="message is-danger">
                                            <div class="message-body">
                                                Erro ao redefinir a senha! O token ou o validador estão inválidos! Por favor, envie outro pedido de redefinição!
                                            </div>
                                        </article>
                                    <?php } else { ?>
                                        <?php if (isset($resetpwd)) { ?>
                                            <article class="message <?php echo $resetpwdclass ?>">
                                                <div class="message-body">
                                                    <?php echo $resetpwd; ?>
                                                </div>
                                            </article>
                                        <?php } ?>
                                        <form action="" method="post">
                                            <input type="hidden" name="selector" value="<?php echo $selector ?>">
                                            <input type="hidden" name="validator" value="<?php echo $validator ?>">
                                            <label for="PASSWORD1" class="label">Nova senha</label>
                                            <div class="field has-addons">
                                                <div class="control has-icons-left is-expanded">
                                                    <input type="password" autocomplete="off" class="input" :class="{'is-primary': validatePassword() == 4, 'is-success': validatePassword() == 3, 'is-warning': validatePassword() == 2, 'is-danger': validatePassword() <= 1}" placeholder="Digite a sua nova senha" v-model="passwd1" name="PASSWORD1" @input="validSubmit()" required>
                                                    <span class="icon is-small is-left">
                                                        <i class="fa fa-lock"></i>
                                                    </span>
                                                    <p class="help is-primary" v-show="validatePassword() == 4">Excelente</p>
                                                    <p class="help is-success" v-show="validatePassword() == 3">Forte</p>
                                                    <p class="help is-warning" v-show="validatePassword() == 2">Médio</p>
                                                    <p class="help is-danger" v-show="validatePassword() == 1">Fraca. Insira uma senha mais forte!</p>
                                                    <p class="help is-danger" v-show="validatePassword() == 0">Muito fraca. Insira uma senha mais forte!</p>
                                                </div>
                                                <div class="control has-icons">
                                                    <button tabindex="-1" type="button" id="toggleIcon" class="button fas fa-eye" onmousedown="pwdShow()" onmouseup="pwdShow()"></button>
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label for="PASSWORD2" class="label">Confirmar a senha</label>
                                                <div class="control has-icons-left has-icons-right">
                                                    <input type="password" autocomplete="off" class="input" :class="{'is-danger': confirmPassword() == false}" placeholder="Confirme a sua senha" v-model="passwd2" name="PASSWORD2" @input="validSubmit()" required>
                                                    <span class="icon is-small is-left">
                                                        <i class="fa fa-lock"></i>
                                                    </span>
                                                    <span class="icon is-small is-right">
                                                        <i class="fas" :class="{'fa-exclamation-triangle' : confirmPassword() == false}"></i>
                                                    </span>
                                                </div>
                                                <p class="help is-danger" v-show="confirmPassword() == false">As senhas não correspondem!</p>
                                            </div>
                                            <button class="button is-info" type="submit" name="reset-password-submit" onclick="this.classList.add('is-loading')" v-bind:disabled="!casePass"> Redefinir senha</button>
                                        </form>
                                    <?php } ?>
                                <?php } else { ?>
                                    <article class="message is-danger">
                                        <div class="message-body">
                                            Erro ao redefinir a senha! O token ou o validador estão inválidos! Por favor, envie outro pedido de redefinição!
                                        </div>
                                    </article>
                                <?php } ?>
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
                    if (this.isValidPassword >= 2 && this.isConfirmPassword == true) {
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
    <script src="https://hatchfy.philadelpho.tk/js/pwdmain.js" async defer></script>
</body>

</html>