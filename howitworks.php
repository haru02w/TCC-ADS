<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>Como Funciona</title>
</head>
<body class="backgroundPrivacy">
<nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            <img src="/images/logochick.png" alt="Logo do HatchFy" width="28" height="28">
            <p class="subtitle is-3 font-face">&nbsp HatchFy</p>
        </a>
        <a role="button" class="navbar-burger" aria-label="menu" :class="{'is-active' : isActiveBurger}" aria-expanded="false" data-target="navbarMenuPage" @click="onClickBurger">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>
    <div id="navbarMenuPage" class="navbar-menu is-transparent" :class="{'is-active': isActiveBurger}">
        <div class="navbar-start">
            <a class="navbar-item" href="/whoweare/">
                Quem somos?
            </a>
            <a class="navbar-item" href="/privacypolicy/">
                Politica de Privacidade
            </a>
            <a class="navbar-item" href="/howitworks/">
                Como Funciona?
            </a>
        </div>
    </div>
</nav>
<br>
    <section class="section">
        <div class="container">
            <h1 class="title has-text-centered">Como Funciona <b class="has-text-link">Hatchfy</b></h1>
            <p class="subtitle has-text-centered">Segue para um passo a passo de como utilizar o site de maneira correta.
            </p>
            <div class="content">
                <h3>Escolha de tipo de Usuário</h3>
                <p>Primeiro de tudo, tenha em mente que tipo de usuário você será:
                    o desenvolvedor que ajudará outros a resolverem problemas,
                    ou um cliente que necessita de alguma solução por meio de projetos.
</p>
                <h3>Cadastro de Conta</h3>
                <p>Assim que souber vá ao canto direito superior e clique em Registrar.
                   Para criar sua conta coloque seu nome completo, pois trabalhamos com 
                   100% de transparência e segurança<br/>
                   •	Seu CPF<br/>
                   •	Um E-mail que você utilize<br/>
                   •	Crie sua Senha<br/>
                   •	Data de Nascimento<br/>
                   •	Escolha o tipo de usuário pensado anteriormente<br/>
                   •	Resolva o Captcha<br/>
                   <h3> Pronto!</h3>
                  Agora você se tornou
                 um utilizador de nossos serviços.
                </p>
            </div>
        </div>
    </section>
</body>
</html>
