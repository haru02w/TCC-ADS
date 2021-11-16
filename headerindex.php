<?php
    if(!isset($protocol)) {
        require("./php/hidephp.php");
    }
?>
<nav class="navbar" role="navigation" aria-label="main navigation">
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
                Como funciona?
            </a>
        </div>
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <button class="button is-info" @click="onClickButtonRegister">
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
