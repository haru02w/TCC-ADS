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
$year = date('Y');

if (isset($_SESSION['TYPE'])) {
  if ($_SESSION['TYPE'] == "CUSTOMER") {
    header("Location: /customermenu/");
  } else if ($_SESSION['TYPE'] == "DEVELOPER") {
    header("Location: /search/");
  }
} else if (isset($_COOKIE['EMAIL']) && isset($_COOKIE['TYPE'])) {
  if ($_COOKIE['TYPE'] == "CUSTOMER") {
    header("Location: /customermenu/");
  } else if ($_COOKIE['TYPE'] == "DEVELOPER") {
    header("Location: /search/");
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Receba ou crie o seu programa agora! Nossa plataforma te ajudará a obter experiência no mercado de trabalho ou ter o seu problema solucionado através de uma aplicação feita por desenvolvedores jovens.">
  <title>Hatchfy</title>
  <link rel="stylesheet" href="/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
  <script src="/js/vue.js"></script>
  <script src="/js/jscript.js"></script>
  <script src="/js/v-mask.min.js"></script>
  <script src="/js/moment.js"></script>
  <script src="/js/zxcvbn.js"></script>
  <script src="https://js.hcaptcha.com/1/api.js?hl=pt&onload=renderCaptcha"></script>
</head>

<body class="background">
  <div id="app" class="script">
    <div class="notification-alert" id="notificationAlert"></div>
    <div class="pageloader is-link is-active"><span class="title">Carregando...</span></div>
    <?php require("./headerindex.php"); ?>
    <section class="hero is-fullheight">
      <div class="hero-body">
        <div class="container has-text-centered">
          <p class="subtitle is-2" id="textColorThree">
             Receba ou crie o seu programa agora!
          </p>
          <p class="subtitle is-4" id="textColorTwo">
            Nossa plataforma te ajudará a obter experiência no mercado de trabalho ou ter o seu problema solucionado 
            através de uma aplicação feita por desenvolvedores em inicio de carreira.
          </p>
          <p class="subtitle is-4" id="textColorTwo">
            Este projeto tem o objetivo de proporcionar ensinamentos sobre Analise e Desenvolvimento de Softwares.<br>  
            Assim como soluções simples para problemas do meio digital sem processos burocráticos<br>
             utilizando a confecção de um software.
          </p>
        </div>
      </div>
    </section>
    <footer class="footer">
      <div class="content has-text-centered">
        <div class="columns ">
          <div class="column">
            <p class="title">Como cliente</p>
            <div class="columns ">
              <div class="column">
                <div class="box">
                  <span class="icon is-large">
                    <i class="fas fa-check fa-2x"></i>
                  </span>
                  <p class="subtitle">Receba um software totalmente gratuito</p>
                </div>
              </div>
              <div class="column">
                <div class="box">
                  <span class="icon is-large">
                    <i class="far fa-thumbs-up fa-2x"></i>
                  </span>
                  <p class="subtitle">Apoie a comunidade de novos programadores</p>
                </div>
              </div>
            </div>
          </div>
          <div class="column">
            <p class="title">Como desenvolvedor</p>
            <div class="columns">
              <div class="column">
                <div class="box">
                  <span class="icon is-large">
                    <i class="fas fa-rocket fa-2x"></i>
                  </span>
                  <p class="subtitle">Melhore suas habilidades de programação</p>
                </div>
              </div>
              <div class="column">
                <div class="box">
                  <span class="icon is-large">
                    <i class="fas fa-shopping-bag fa-2x"></i>
                  </span>
                  <p class="subtitle">Ganhe experiência no mercado de trabalho</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <?php require "baseboard.php"?>
    <!-- REGISTER MODAL-->
    <div class="modal" :class="{'is-active': isActiveRegister}">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Registrar</p>
          <button class="delete" aria-label="close" @click="onClickButtonRegister"></button>
        </header>
        <section class="modal-card-body">
          <form action="#" class="box" method="POST" id="registerForm">
            <article tabindex="-1" class="message" style="display: none;">
              <div class="message-body">
              </div>
            </article>
            <div class="field">
              <label for="NAME_REGISTER" class="label">Nome Completo</label>
              <div class="control has-icons-left">
                <input type="text" class="input" autocomplete="off" placeholder="Digite seu nome" name="NAME_REGISTER" id="NAME_REGISTER" @input="validSubmitRegister" required v-model="registerName">
                <span class="icon is-small is-left">
                  <i class="fa fa-user"></i>
                </span>
              </div>
            </div>
            <div class="field">
              <label for="CPF_REGISTER" class="label">CPF</label>
              <div class="control has-icons-left has-icons-right">
                <input type="tel" autocomplete="off" class="input" :class="{'is-success': validateCpf() == true, 'is-danger': validateCpf() == false}" placeholder="Digite seu CPF" id="CPF_REGISTER" name="CPF_REGISTER" v-model="registerCpf" v-mask="'###.###.###-##'" @input="validSubmitRegister" required>
                <span class="icon is-small is-left">
                  <i class="fa fa-address-card"></i>
                </span>
                <span class="icon is-small is-right">
                  <i class="fas" :class="{'fa-check' : validateCpf() == true, 'fa-exclamation-triangle' : validateCpf() == false}"></i>
                </span>
                <p class="help is-success" v-show="validateCpf() == true">Esse CPF é válido</p>
                <p class="help is-danger" v-show="validateCpf() == false">Esse CPF é inválido</p>
              </div>
            </div>
            <div class="field">
              <label for="EMAIL_REGISTER" class="label">Email</label>
              <div class="control has-icons-left has-icons-right">
                <input type="email" autocomplete="off" class="input" :class="{'is-success': validateEmail() == true, 'is-danger': validateEmail() == false}" placeholder="Digite seu email" id="EMAIL_REGISTER" name="EMAIL_REGISTER" @input="validSubmitRegister" required v-model="registerEmail">
                <span class="icon is-small is-left">
                  <i class="fa fa-envelope"></i>
                </span>
                <span class="icon is-small is-right">
                  <i class="fas" :class="{'fa-check' : validateEmail() == true, 'fa-exclamation-triangle' : validateEmail() == false}"></i>
                </span>
              </div>
              <p class="help is-success" v-show="validateEmail() == true">Esse e-mail é válido</p>
              <p class="help is-danger" v-show="validateEmail() == false">Esse e-mail é inválido</p>
            </div>
            <label for="PASSWORD1" class="label">Senha</label>
            <div class="field has-addons">
              <div class="control has-icons-left is-expanded">
                <input type="password" autocomplete="off" class="input" :class="{'is-primary': validatePassword() == 4, 'is-success': validatePassword() == 3, 'is-warning': validatePassword() == 2, 'is-danger': validatePassword() <= 1}" placeholder="Digite sua senha" v-model="passwd1" id="PASSWORD1" name="PASSWORD1" @input="validSubmitRegister" required>
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
                <button tabindex="-1" type="button" id="toggleIconR" class="button fas fa-eye" onmousedown="pwdShowR()" onmouseup="pwdShowR()"></button>
              </div>
            </div>
            <div class="field">
              <label for="PASSWORD2" class="label">Confirmar a senha</label>
              <div class="control has-icons-left has-icons-right">
                <input type="password" autocomplete="off" class="input" :class="{'is-danger': confirmPassword() == false}" placeholder="Confirme a sua senha" v-model="passwd2" id="PASSWORD2" name="PASSWORD2" @input="validSubmitRegister" required>
                <span class="icon is-small is-left">
                  <i class="fa fa-lock"></i>
                </span>
                <span class="icon is-small is-right">
                  <i class="fas" :class="{'fa-exclamation-triangle' : confirmPassword() == false}"></i>
                </span>
              </div>
              <p class="help is-danger" v-show="confirmPassword() == false">As senhas não correspondem!</p>
            </div>
            <div class="field">
              <label for="BIRTH_DATE" class="label">Data de nascimento</label>
              <div class="control has-icons-left has-icons-right">
                <input type="tel" autocomplete="off" class="input" :class="{'is-success': validateDate() == true, 'is-danger': validateDate() == false}" placeholder="Digite sua data de nascimento" id="BIRTH_DATE" name="BIRTH_DATE" v-model="registerDate" required v-mask="'##/##/####'" @input="validSubmitRegister">
                <span class="icon is-small is-left">
                  <i class="fa fa-birthday-cake"></i>
                </span>
                <span class="icon is-small is-right">
                  <i class="fas" :class="{'fa-check' : validateDate() == true, 'fa-exclamation-triangle' : validateDate() == false}"></i>
                </span>
              </div>
              <p class="help is-success" v-show="validateDate() == true">A data é válida!</p>
              <p class="help is-danger" v-show="validateDate() == false">A data é inválida!</p>
            </div>
            <div class="field">
              <label for="CONTACT" class="label">Telefone</label>
              <div class="control has-icons-left">
                <input type="tel" autocomplete="on" class="input" placeholder="Digite aqui o seu telefone" id="CONTACT" name="CONTACT" @input="validSubmitRegister" v-model="registerContact" v-mask='maskTel()'>
                <span class="icon is-small is-left">
                  <i class="fas fa-phone"></i>
                </span>
              </div>
            </div>
            <label for="TYPE_REGISTER" class="label">Tipo de usuário</label>
            <div class="control has-icons-left">
              <div class="select">
                <select name="TYPE_REGISTER" id="TYPE_REGISTER" required @change="validSubmitRegister" v-model="registerSelect">
                  <option value="" disabled selected>Selecione</option>
                  <option value="CUSTOMER">Cliente</option>
                  <option value="DEVELOPER">Desenvolvedor</option>
                </select>
                <div class="icon is-small is-left">
                  <i class="fas fa-users"></i>
                </div>
              </div>
            </div>
            <br>
            <div class="field">
              <div class="control">
                <div id="registerCaptcha"></div>
              </div>
            </div>
            <br>
            <div class="field">
              <div class="buttons is-right">
                <button type="submit" id="register" v-bind:disabled="!casesRegister" class="button is-success">Cadastrar</button>
              </div>
            </div>
          </form>
        </section>
      </div>
    </div>
    <!--LOGIN MODAL-->
    <div class="modal" :class="{'is-active': isActiveLogin}">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Entrar</p>
          <button class="delete" aria-label="close" @click="onClickButtonLogin"></button>
        </header>
        <section class="modal-card-body">
          <form action="#" class="box" method="POST" id="loginForm">
            <article tabindex="-1" class="message is-danger" style="display: none;">
              <div class="message-body">
              </div>
            </article>
            <div class="field">
              <label for="EMAIL_LOGIN" class="label">Email</label>
              <div class="control has-icons-left">
                <input type="email" autocomplete="off" class="input" placeholder="Digite seu e-mail" id="EMAIL_LOGIN" name="EMAIL_LOGIN" v-model="loginEmail" required @input="validSubmitLogin">
                <span class="icon is-small is-left">
                  <i class="fa fa-envelope"></i>
                </span>
              </div>
            </div>
            <label for="PASSWORD_LOGIN" class="label">Senha</label>
            <div class="field has-addons">
              <div class="control has-icons-left is-expanded">
                <input type="password" autocomplete="off" class="input" placeholder="Digite sua senha" id="PASSWORD_LOGIN" name="PASSWORD_LOGIN" required v-model="loginPasswd" @input="validSubmitLogin">
                <span class="icon is-small is-left">
                  <i class="fa fa-lock"></i>
                </span>
              </div>
              <div class="control has-icons">
                <button tabindex="-1" type="button" id="toggleIconL" class="button fas fa-eye" onmousedown="pwdShowL()" onmouseup="pwdShowL()"></button>
              </div>
            </div>
            <div class="control has-icons-left">
              <label for="TYPE_LOGIN" class="label">Tipo de usuário</label>
              <div class="select">
                <select name="TYPE_LOGIN" id="TYPE_LOGIN" required @change="validSubmitLogin" v-model="loginSelect">
                  <option value="" disabled selected>Selecione</option>
                  <option value="CUSTOMER">Cliente</option>
                  <option value="DEVELOPER">Desenvolvedor</option>
                </select>
                <div class="icon is-small is-left">
                  <i class="fas fa-users"></i>
                </div>
              </div>
            </div>
            <br>
            <div class="field">
              <div class="control">
                <div id="loginCaptcha"></div>
              </div>
            </div>
            <div class="field">
              <div class="control">
                <label class="checkbox">
                  <input type="checkbox" class="checkbox" name="remember">
                  Lembrar de mim
                </label>
              </div>
            </div>
            <div class="field">
              <div class="control">
                <a href="/resetpassword/"> Esqueceu a senha? </a>
              </div>
            </div>
            <br>
            <div class="field">
              <div class="buttons is-right">
                <button type="submit" id="login" class="button is-success" :disabled="!casesLogin">Logar</button>
              </div>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
  <noscript> <style> .script { display: none; } </style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
  <script>
    Vue.directive('mask', VueMask.VueMaskDirective);
    var vue = new Vue({
      el: '#app',
      data: {
        isActiveRegister: false,
        isActiveLogin: false,
        isActiveBurger: false,
        registerName: "",
        registerCpf: "",
        registerEmail: "",
        registerContact: "",
        passwd1: "",
        passwd2: "",
        registerDate: "",
        registerSelect: "",
        isValidDateRegister: "",
        isValidEmailRegister: "",
        isValidCpfRegister: "",
        isValidPassword: "",
        isConfirmPassword: "",
        loginEmail: "<?php if (isset($_GET['email'])) { $email = $_GET['email']; echo "$email"; } ?>",
        loginPasswd: "",
        loginSelect: "",
        casesRegister: false,
        casesLogin: false,
      },
      methods: {
        onClickButtonRegister() {
          this.isActiveRegister = !this.isActiveRegister;
        },
        onClickButtonLogin() {
          this.isActiveLogin = !this.isActiveLogin;
        },
        onClickBurger() {
          this.isActiveBurger = !this.isActiveBurger;
        },
        validateCpf() {
          if (this.registerCpf != "" && this.registerCpf.length == 14) {
            if (validar(this.registerCpf)) {
              this.isValidCpfRegister = true;
              return true;
            } else {
              this.isValidCpfRegister = false;
              return false;
            }
          }
        },
        validateEmail() {
          var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
          if (this.registerEmail != "") {
            if (this.registerEmail.match(mailformat)) {
              this.isValidEmailRegister = true;
              return true;
            } else {
              this.isValidEmailRegister = false;
              return false;
            }
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
        validateDate() {
          let registerDateArray = this.registerDate.split('/');
          if (this.registerDate != "" && this.registerDate.length == 10) {
            if (registerDateArray[2] < <?php echo "$year - 100" ?> || registerDateArray[2] > <?php echo "$year" ?>) {
              this.isValidDateRegister = false;
              return false;
            } 
            else {
              this.isValidDateRegister = moment(this.registerDate, 'DD/MM/YYYY').isValid();
              if (this.isValidDateRegister != true) {
                this.isValidDateRegister = false;
                return false;
              } 
              else {
                this.isValidDateRegister = true;
                return true;
              }
            }
          }
        },
        validSubmitRegister() {
          if (this.registerName != "" && this.registerCpf != "" && this.registerEmail != "" && this.passwd1 != "" && this.passwd2 != "" && this.registerDate != "" && this.registerSelect != "" && this.registerContact != "") {
            this.isValidCpfRegister = this.validateCpf();
            this.isValidDateRegister = this.validateDate();
            this.isValidEmailRegister = this.validateEmail();
            this.isConfirmPassword = this.confirmPassword();
            this.isValidPassword = this.validatePassword();
            if (this.isValidCpfRegister == true && this.isValidDateRegister == true && this.isValidEmailRegister == true && this.isConfirmPassword == true && this.isValidPassword >= 1) {
              this.casesRegister = true;
            } 
            else {
              this.casesRegister = false;
            }
          } 
          else {
            this.casesRegister = false;
          }
        },
        validSubmitLogin() {
          if (this.loginEmail != "" && this.loginPasswd != "" && this.loginSelect != "") {
            var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            if (this.loginEmail.match(mailformat)) {
              this.casesLogin = true;
            } 
            else {
              this.casesLogin = false;
            }
          } 
          else {
            this.casesLogin = false;
          }
        },
        maskTel() {
          if(!!this.registerContact) {
            return this.registerContact.length == 15 ? '(##) #####-####' : '(##) ####-#####'
          } 
          else {
            return '(##) #####-####'
          }
        },
      }
    })
  </script>
  <script src="/js/indexmain.js" async defer></script>
  <script> function renderCaptcha() { var params = { "sitekey": "4e9fd5af-ad94-43d0-8888-cf905e63b65f", }; lCaptcha = hcaptcha.render("loginCaptcha", params); rCaptcha = hcaptcha.render("registerCaptcha", params); } </script>
  <script>
    <?php if (isset($_SESSION['indexmsg'])) {
      $indexclass = $_SESSION['indexclass'];
      $indexmsg = $_SESSION['indexmsg'];
      echo "var notif = document.querySelector('#notificationAlert'); notif.classList.add('$indexclass'); notif.innerHTML = '$indexmsg';";
      unset($_SESSION['indexmsg']);
      unset($_SESSION['indexclass']);
    } ?>
    var pgload = document.querySelector('#app .pageloader');
    window.onload = () => {
      pgload.classList.remove('is-active');
      <?php 
        if(isset($indexmsg)) {
          echo "notif.classList.add('show'); setTimeout(function () { notif.classList.remove('show');} , 6000);";
        }
      ?>
    }
  </script>  
</body>
</html>
