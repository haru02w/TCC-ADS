<?php
    if(!isset($protocol)) {
        require("./php/hidephp.php");
    }
?>
<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            <img src="/images/logochick.png" alt="imagem da logo">
            <p class="subtitle is-3 font-face">&nbsp HatchFy</p>
        </a>

        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" @click="onClickBurger" :class="{'is-active' : isActiveBurger}">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div class="navbar-menu" :class="{'is-active' : isActiveBurger}">
        <div class="navbar-start">
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Serviços
                </a>
                <div class="navbar-dropdown">
                    <a class="navbar-item" href="/customermenu/">
                        Meus serviços
                    </a>
                    <a class="navbar-item" href="/createservice/">
                        Crie um serviço
                    </a>
                    <a class="navbar-item" href="/pendingservices/">
                        Serviços pendentes
                    </a>
                    <a class="navbar-item" href="/developmentservices/">
                        Serviços em desenvolvimento
                    </a>
                    <a class="navbar-item" href="/doneservices/">
                        Serviços concluídos
                    </a>
                </div>
            </div>
                <a class="navbar-item" href="/account/"> Minha conta </a>
        </div>
        <div class="navbar-end">
            <div class="navbar-item">
              <a href="/logout/">
                <button class="button is-danger">
                    <span> Sair </span>
                    <span class="icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </span>
                </button>
              </a>
            </div>
        </div>
    </div>
</nav>
