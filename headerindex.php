<nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            <img src="/images/logochick.png" alt="imagem da logo">
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
            <a class="navbar-item" href="whoweare.php">
                Quem somos?
            </a>
            <a class="navbar-item" href="privacypolicy.php">
                LGPD
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