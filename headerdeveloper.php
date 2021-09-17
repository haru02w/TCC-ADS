<nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
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
                    <a class="navbar-item" href="/developermenu/">
                        Meus serviços
                    </a>
                    <a class="navbar-item" href="/search/" >
                        Serviços disponíveis
                    </a>
                    <a class="navbar-item" href="/pendingservices/">
                        Serviços pendentes
                    </a>
                    <a  class="navbar-item" href="/developmentservices/">
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
                <form action="/search/" method="GET">
                    <div class="field has-addons">
                        <div class="control">
                            <input type="search" class="input is-rounded" placeholder="Procurar serviços..." name="q">
                        </div>
                        <div class="control has-icons">
                            <button type="submit" class="fas fa-search button is-rounded"></button>
                        </div>
                    </div>
                </form>
            </div>
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
