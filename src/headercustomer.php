<nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            <p class="subtitle is-3 font-face">HatchFy</p>
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
                    <a class="navbar-item" href="customermenu.php">
                        Meus serviços
                    </a>
                    <a class="navbar-item" href="createservice.php">
                        Crie um serviço
                    </a>
                    <a class="navbar-item" href="pendingservices.php">
                        Serviços pendentes
                    </a>
                    <a class="navbar-item" href="developmentservices.php">
                        Serviços em desenvolvimento
                    </a>
                    <a class="navbar-item" href="doneservices.php">
                        Serviços concluídos
                    </a>
                </div>
            </div>
                <a class="navbar-item" href="account.php"> Minha conta </a>
        </div>
        <div class="navbar-end">
            <div class="navbar-item">
                <button class="button is-danger" @click="onClickLogout">
                    <span> Sair </span>
                    <span class="icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </span>
                </button>
            </div>
        </div>
    </div>
</nav>
