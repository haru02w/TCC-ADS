<?php
    session_name("HATIDS");
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    
    if (isset($_POST["reset-request-submit"])) {
        require("connection.php");
        require("functions.php");

        $email = filter_input(INPUT_POST, "EMAILPWD_RESET", FILTER_VALIDATE_EMAIL);

        $result1 = searchEmailType($email, "DEVELOPER", $conn);
        $result2 = searchEmailType($email, "CUSTOMER", $conn);
    
        if (is_null(mysqli_fetch_assoc($result1)) && is_null(mysqli_fetch_assoc($result2))) {
            $resetpwd = "Usuário não encontrado!";
            $resetpwdclass = "is-danger";
        } 
        else {
        
            $selector = bin2hex(random_bytes(8));
            $token = random_bytes(50);
            $url = "https://hatchfy.philadelpho.tk/createnewpassword/" . $selector . "/" . bin2hex($token) . "/";
            $expires = date("U") + 1800;

            $stmt = mysqli_prepare($conn, "DELETE FROM TB_PASSWORDRESET WHERE EMAIL = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $hashed = password_hash($token, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($conn, "INSERT INTO TB_PASSWORDRESET (EMAIL, SELECTOR, TOKEN, EXPIRES) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "ssss", $email, $selector, $hashed, $expires);
            mysqli_stmt_execute($stmt);
            
            if(mysqli_num_rows($result1) >= 1) {
                $type = "DEVELOPER";
            }
            else if(mysqli_num_rows($result2) >= 1) {
                $type = "CUSTOMER";   
            }
            $rowuser = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
            $name = $rowuser['NAME'];
            $content = '<!DOCTYPE html>
                <html lang="pt-BR">
                
                <head>
                    <meta charset="UTF-8">
                    <style>
                        .wrapper {
                            padding: 20px;
                            color: #444;
                            font-size: 1.3em;
                        }
                
                        a {
                            background: #00FA9A;
                            text-decoration: none;
                            padding: 8px 15px;
                            border-radius: 5px;
                            color: #ffffff;
                        }
                    </style>
                </head>
                
                <body>
                    <div class="wrapper">
                        <p> Olá ' . $name . '!</p>
                        <p> Para redefinir a sua senha, por favor clique no link abaixo! Se não foi você que solicitou essa mudança de senha, por favor, ignore este email!</p>
                        <a href="' . $url . '">Redefinir senha!</a>
                    </div>
                </body>
                </html>';


            $subject = "Redefinição de senha do HatchFy!";
            $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

            if(!sendEmail($email, $subject, $content)) {
                $resetpwd = "Um link para redefinir a sua senha foi enviado em seu email e irá expirar em 30 minutos!";
                $resetpwdclass = "is-success";
            }
            else {
                $resetpwd = "Falha ao solicitar a redefinição de senha, por favor, tente novamente mais tarde!";
                $resetpwdclass = "is-danger";
            }
            mysqli_stmt_close($stmt);
        }
        
        mysqli_close($conn);
    }
    
    if(isset($_SESSION['resetpwd']) && isset($_SESSION['resetpwdclass'])) {
        $resetpwd = $_SESSION['resetpwd'];
        $resetpwdclass = $_SESSION['resetpwdclass'];
    }
    
?>
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
                                <?php if (isset($resetpwd)) { ?>
                                    <article class="message <?php echo $resetpwdclass ?>">
                                        <div class="message-body">
                                            <?php echo $resetpwd; ?>
                                            <?php session_unset(); ?>
                                        </div>
                                    </article>
                                <?php } ?>
                                <form action="" method="post">
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
                                    <button class="button is-info" type="submit" onclick="this.classList.add('is-loading')" name="reset-request-submit" v-bind:disabled="!casePass"> Enviar email</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <noscript> <style> .script { display: none; } </style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
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
                },
            }
        })
    </script>
</body>

</html>