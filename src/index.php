<?php 
  session_start();

  date_default_timezone_set('America/Sao_Paulo'); 
  $year = date('Y');

  if(isset($_SESSION['TYPE'])){
    $type = $_SESSION['TYPE'];

    if($type == "CUSTOMER"){
      header("Location: /customermenu.php");
    }
    else if($type == "DEVELOPER"){
      header("Location: /developermenu.php");
    }
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
  <script type="text/javascript" src="js/zxcvbn.js"></script>

</head>

<body class="background">
  <!-- VUE.JS DIV -->
  <div id="app">
    <!-- PAGE HEADER NAVBAR -->
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
      <div class="navbar-brand">
        <a class="navbar-item" href="/">
          <p class="subtitle is-3 font-face">HatchFy</p>
        </a>

        <a role="button" class="navbar-burger" aria-label="menu" :class="{'is-active' : isActiveBurger}" aria-expanded="false" data-target="navbarMenuPage" @click="onClickBurger">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <div id="navbarMenuPage" class="navbar-menu is-transparent" :class="{'is-active': isActiveBurger}">
        <div class="navbar-end">
          <div class="navbar-item">
            <div class="buttons">
              <button class="button is-primary" @click="onClickButtonRegister">
                <strong>Registrar</strong>
              </button>
              <button class="button is-light" @click="onClickButtonLogin">
                Entrar
              </button>
           </div>
         </div>
        </div>
      </div>
    </nav>
    <!-- PAGE MAIN HERO-->
    <section class="hero is-fullheight">
      <div class="hero-body">
        <div class="container has-text-centered">
          <p class="title is-2">HatchFy</p>
          <p class="subtitle is-4">Receba ou crie o seu programa agora!</p>
          <p class="subtitle is-4">
              Nossa plataforma te ajudará a obter
              experiência no mercado de trabalho
              ou ter o seu problema solucionado 
              através de uma aplicação feita por desenvolvedores jovens.
          </p>
        </div>
      </div>
    </section>
    <!-- PAGE FOOTER -->
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
    <!-- REGISTER MODAL-->
    <div class="modal" :class="{'is-active': isActiveRegister}">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Registrar</p>
          <button class="delete" aria-label="close" @click="onClickButtonRegister"></button>
        </header>
        <section class="modal-card-body">
          <form action="register.php" class="box" method="POST" name="registerForm">

            <div class="field">
              <h3 class="title is-1 has-text-dark has-text-centered">Crie Sua Conta!</h3>
              <label for="NAME_REGISTER" class="label">Nome Completo</label>
              <div class="control has-icons-left">
                <input type="text" class="input" placeholder="Digite seu nome" name="NAME_REGISTER" id="NAME_REGISTER" @click="validSubmitRegister" required 
                v-model="registerName" @blur="validSubmitRegister">
                <span class="icon is-small is-left">
                  <i class="fa fa-user"></i>
                </span>
              </div>
            </div>

            <div class="field">
            <label for="CPF_REGISTER" class="label">CPF</label>
              <div class="control has-icons-left has-icons-right">
                <input type="tel" class="input" :class="{'is-success': validateCpf() == true, 'is-danger': validateCpf() == false}" placeholder="Digite seu CPF" name="CPF_REGISTER" id="CPF_REGISTER" v-model="registerCpf" required
                  v-mask="'###.###.###-##'" @blur="validSubmitRegister" @click="validSubmitRegister">
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
                <input type="email" class="input" :class="{'is-success': validateEmail() == true, 'is-danger': validateEmail() == false}" placeholder="Digite seu email" name="EMAIL_REGISTER" id="EMAIL_REGISTER" 
                @click="validSubmitRegister" required v-model="registerEmail" @blur="validSubmitRegister">
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

            <div class="field">
            <label for="PASSWORD1" class="label">Senha</label>
              <div class="control has-icons-left">
                <input type="password" class="input" :class="{'is-primary': validatePassword() == 4, 'is-success': validatePassword() == 3, 'is-warning': validatePassword() == 2, 'is-danger': validatePassword() <= 1}" placeholder="Digite sua senha" v-model="passwd1" 
                name="PASSWORD1" id="PASSWORD1" @click="validSubmitRegister" required @blur="validSubmitRegister">
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
                <input type="password" class="input" :class="{'is-danger': confirmPassword == false }"placeholder="Confirme a sua senha" v-model="passwd2"
                  name="PASSWORD2" id="PASSWORD2" @blur="validSubmitRegister" @click="validSubmitRegister" required>
                <span class="icon is-small is-left">
                  <i class="fa fa-lock"></i>
                </span>
                <span class="icon is-small is-right">
                  <i class="fas" :class="{'fa-exclamation-triangle' : confirmPassword() == false }"></i>
                </span>
              </div>
              <p class="help is-danger" v-show="confirmPassword() == false">As senhas não correspondem!</p>
            </div>

            <div class="field">
            <label for="BIRTH_DATE" class="label">Data de nascimento</label>
              <div class="control has-icons-left has-icons-right">
                <input type="tel" class="input" :class="{'is-active': validateDate() == true, 'is-danger': validateDate() == false}" placeholder="Digite sua data de nascimento" name="BIRTH_DATE"
                  v-model="registerDate" id="BIRTH_DATE" required v-mask="'##/##/####'" @blur="validSubmitRegister" 
                  @click="validSubmitRegister">
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

            <label for="TYPE_REGISTER" class="label">Tipo de usuário</label>
            <div class="control has-icons-left">
              <div class="select">
                <select name="TYPE_REGISTER" id="TYPE_REGISTER" required @click="validSubmitRegister" v-model="registerSelect" @blur="validSubmitRegister">
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
              <div class="buttons is-right">
                <button type="submit" v-bind:disabled="!casesRegister" class="button is-success">Cadastrar</button>
              </div>
            </div>

          </form>
        </section>
      </div>
    </div>
    <!-- LOGIN MODAL-->
    <div class="modal " :class="{'is-active': isActiveLogin}">
      <div class="modal-background"></div>
      <div class="modal-card">
        <header class="modal-card-head">
          <p class="modal-card-title">Entrar</p>
          <button class="delete" aria-label="close" @click="onClickButtonLogin"></button>
        </header>
        <section class="modal-card-body">
          <form action="login.php" class="box" method="POST" name="loginForm">
            
            <div class="field">
              <h1 class="title is-1 has-text-dark has-text-centered">Login</h1>
              <label for="EMAIL_LOGIN" class="label">Email</label>
              <div class="control has-icons-left">
                <input type="email" class="input" placeholder="Digite seu e-mail" name="EMAIL_LOGIN" id="EMAIL_LOGIN" 
                v-model="loginEmail" required @click="validSubmitLogin" @blur="validSubmitLogin">
                <span class="icon is-small is-left">
                  <i class="fa fa-envelope"></i>
                </span>
              </div>
            </div>

            <div class="field">
              <label for="PASSWORD_LOGIN"class="label">Senha</label>
              <div class="control has-icons-left">
                <input type="password" class="input" placeholder="Digite sua senha" name="PASSWORD_LOGIN" id="PASSWORD" required
                v-model="loginPasswd" @click="validSubmitLogin" @blur="validSubmitLogin">
                <span class="icon is-small is-left">
                  <i class="fa fa-lock"></i>
                </span>
              </div>
            </div>

            <label for="TYPE_LOGIN" class="label">Tipo de usuário</label>
            <div class="control has-icons-left">
              <div class="select">
                <select name="TYPE_LOGIN" id="TYPE_LOGIN" required @click="validSubmitLogin" v-model="loginSelect" @blur="validSubmitLogin">
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
                <label class="checkbox">
                  <input type="checkbox" class="checkbox">
                    Lembrar de mim    
                  </label>
              </div>
            </div>

            <div class="field">
              <div class="buttons is-right">
                <button type="submit" class="button is-success" :disabled="!casesLogin">Logar</button>
              </div>
            </div>

          </form>
        </section>
      </div>
    </div>
    <!-- RETURN MODAL -->
    <div class="modal" :class="topModalReturn">
      <div class="modal-background"></div>
      <div class="modal-content">
        <div class="box">
          <article class="message" :class="messageModalReturn">
            <div class="message-header">
              <p v-if="isActiveReturn == 'successr' || isActiveReturn == 'verified'">Sucesso</p>
              <p v-else-if="isActiveReturn == 'failurer' || isActiveReturn == 'failurel' || isActiveReturn == 'foundfailurer' || isActiveReturn == 'notverified'">Falha</p>
              <p v-else-if="isActiveReturn == 'expired' || isActiveReturn == 'averified'">Aviso</p>
              <button class="delete" aria-label="close" @click="onClickButtonReturn(); redirectRegister()" v-if="isActiveReturn == 'failurer' || isActiveReturn == 'foundfailurer'"></button>
              <button class="delete" aria-label="close" @click="onClickButtonReturn(); redirectLogin()" v-if="isActiveReturn == 'failurel'"></button>
              <button class="delete" aria-label="close" @click="onClickButtonReturn" v-if="isActiveReturn == 'successr' || isActiveReturn == 'expired' || isActiveReturn == 'verified' || isActiveReturn == 'notverified' || isActiveReturn == 'averified'"></button>
            </div>
            <div v-if="isActiveReturn == 'successr'" class="message-body">
              A sua conta foi cadastrada com sucesso! Para acessar o sistema, entre em seu email e clique no link de verificação que enviamos a você!
            </div>
            <div v-else-if="isActiveReturn == 'failurer'" class="message-body">
              A sua conta não foi cadastrada! Houve algum problema de conexão, por favor, tente novamente mais tarde.
            </div>
            <div v-else-if="isActiveReturn == 'foundfailurer'" class="message-body">
              O CPF ou email inseridos já estão cadastrados no site!
            </div>
            <div v-else-if="isActiveReturn == 'failurel'" class="message-body">
              O e-mail e a senha inseridos não correspondem aos nossos registros. Por favor, verifique os dados e tente novamente.
            </div>
            <div v-else-if="isActiveReturn == 'expired'" class="message-body">
              A sua sessão expirou! Por favor, logue no sistema novamente!
            </div>
            <div v-else-if="isActiveReturn == 'verified'" class="message-body">
              A sua conta foi verificada com sucesso!
            </div>
            <div v-else-if="isActiveReturn == 'notverified'" class="message-body">
              A sua conta ainda não foi verificada! Para verificar, entre em seu email e clique no link de verificação!
            </div>
            <div v-else-if="isActiveReturn == 'averified'" class="message-body">
              A sua conta já foi verificada!
            </div>
          </article>
        </div>
      </div>
    </div>
</div>

  <script>
    // DIRETIVA MASK VUE
    Vue.directive('mask', VueMask.VueMaskDirective);
  </script>

  <!-- VUE.JS SCRIPT -->
  <script>
    Vue.config.devtools = false
    new Vue({
      el: '#app',
      data: {
        isActiveRegister: false,
        isActiveLogin: false,
        isActiveBurger: false,
        isActiveReturn: "<?php if(isset($_SESSION['r'])){ $r = $_SESSION['r']; echo "$r"; session_destroy(); } else if(isset($_SESSION['l'])){ $l = $_SESSION['l']; echo "$l"; session_destroy(); } else if(isset($_SESSION['s'])){ $s = $_SESSION['s']; echo "$s"; session_destroy();} else if(isset($_SESSION['v'])) { $v = $_SESSION['v']; echo $v; session_destroy(); } ?>",
        registerName: "",
        registerCpf: "",
        registerEmail: "",
        passwd1: "",
        passwd2: "",
        registerDate: "",
        registerSelect: "",
        isValidDateRegister: "",
        isValidEmailRegister: "",
        isValidCpfRegister: "",
        isValidPassword: "",
        isConfirmPassword: "",
        loginEmail: "<?php if(isset($_GET['email'])){ $email = $_GET['email']; echo "$email"; }?>",
        loginPasswd: "",
        loginSelect: "",
        casesRegister: false,
        casesLogin: false
      },
      computed: {
        topModalReturn : function () {
          return {
            'is-active': this.isActiveReturn == "successr" || this.isActiveReturn == "failurel" || this.isActiveReturn == "failurer" || this.isActiveReturn == "expired" || this.isActiveReturn == "foundfailurer" || this.isActiveReturn == "verified" || this.isActiveReturn == "notverified" || this.isActiveReturn == 'averified', 
          }
        },
        messageModalReturn : function () {
          return {
            'is-success': this.isActiveReturn == "successr" || this.isActiveReturn == "verified",
            'is-danger': this.isActiveReturn == "failurel" || this.isActiveReturn == "failurer" || this.isActiveReturn == "foundfailurer" || this.isActiveReturn == "notverified",
            'is-warning': this.isActiveReturn == "expired" || this.isActiveReturn == 'averified'
          }
        }
      },
      methods: {
        onClickButtonRegister() {
          this.isActiveRegister = !this.isActiveRegister
        },
        onClickButtonLogin() {
          this.isActiveLogin = !this.isActiveLogin
        },
        onClickBurger() {
          this.isActiveBurger = !this.isActiveBurger
        },
        onClickButtonReturn() {
          this.isActiveReturn = !this.isActiveReturn
        },
        validateCpf() {
          if (this.registerCpf != "") {
            if (validar(this.registerCpf)) {
              this.isValidCpfRegister = true
              return true
            }
            else {
              this.isValidCpfRegister = false
              return false
            }
          }
        },
        redirectLogin() {
          this.isActiveLogin = true
        },
        redirectRegister() {
          this.isActiveRegister = true
        },
        validateEmail() {
          var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/
          
          if(this.registerEmail != ""){
            if(this.registerEmail.match(mailformat)){
              this.isValidEmailRegister = true
              return true
            }
            else{
              this.isValidEmailRegister = false
              return false
            }
          }
        },
        validatePassword() {
          if(this.passwd1 != "") {
            resultado = zxcvbn(this.passwd1)
            return resultado.score
          }
        },
        confirmPassword() {
          if(this.passwd1 != "" && this.passwd2 != ""){
            if(this.passwd1 != this.passwd2){
              return false
            }
            return true
          }
        },
        validateDate() {
          let registerDateArray = this.registerDate.split('/') 
          if(this.registerDate != "" && this.registerDate.length == 10) {
            if(registerDateArray[2] < <?php echo "$year - 100"?> || registerDateArray[2] > <?php echo "$year"?>) {
              this.isValidDateRegister = false
              return false
            }
            else {
              this.isValidDateRegister = moment(this.registerDate, 'DD/MM/YYYY').isValid()
              
              if(this.isValidDateRegister != true){
                this.isValidDateRegister = false
                return false
              }
              else {
                this.isValidDateRegister = true
		            return true
              }
            }
          }
        },
        validSubmitRegister() {
          if(this.registerName != "" && this.registerCpf != "" && this.registerEmail != "" && this.passwd1 != "" && this.passwd2 != "" && this.registerDate != "" && this.registerSelect != ""){
            
            this.isValidCpfRegister = this.validateCpf()
            this.isValidDateRegister = this.validateDate()
            this.isValidEmailRegister = this.validateEmail()
            this.isConfirmPassword = this.confirmPassword()
            this.isValidPassword = this.validatePassword()

            if(this.isValidCpfRegister == true && this.isValidDateRegister == true && this.isValidEmailRegister == true && this.isConfirmPassword == true && this.isValidPassword >= 1){
              this.casesRegister = true
            }
            else{
              this.casesRegister = false
            }
          
          }
          else {
            this.casesRegister = false
          }
        },
        validSubmitLogin() {
          if(this.loginEmail != "" && this.loginPasswd != "" && this.loginSelect != "") {
            this.casesLogin = true
          }
          else{
            this.casesLogin = false
          }
        }
      }
    })
  </script>
</body>

</html>




