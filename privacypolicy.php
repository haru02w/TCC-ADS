<?php
require("./php/hidephp.php");
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <script src="/js/vue.js"></script>
    <title>Politicas de privacidade e Termos de Uso</title>
</head>

<body class="backgroundPrivacy">
    <div id="app">
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="/">
                    <img src="/images/logochick.png" alt="Logo do HatchFy" width="28" height="28">
                    <p class="subtitle has-text-link is-3 font-face">&nbsp HatchFy</p>
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
            </div>
        </nav>
        <section class="section hero is-fullheight">
            <div class="container">
                <h1 class="title has-text-centered">Politica de privacidade e Termos de Uso </h1>
                <p class="subtitle has-text-centered">Ao concordar com a nossa politica de privacidade e os Termos de Uso, você concorda com tudo que está descrito abaixo.</p>
                <div class="content">
                    <h2>Política Privacidade</h2>
                    <p>A sua privacidade é importante para nós. É nossa política respeitar a sua privacidade em
                        relação a qualquer informação sua que possamos coletar no site e
                        outros sites que possuímos e operamos.
                    </p>
                    <p>Solicitamos informações pessoais apenas quando realmente precisamos delas
                        para lhe fornecer um serviço. Fazemo-lo por meios justos e legais, com o seu conhecimento e
                        consentimento.
                        Também
                        informamos por que estamos coletando e como será usado. </p>
                    <p>Apenas retemos as informações coletadas pelo tempo
                        necessário para fornecer o serviço solicitado. Quando armazenamos dados, protegemos dentro de meios
                        comercialmente
                        aceitáveis ​​para evitar perdas e roubos, bem como acesso, divulgação, cópia, uso ou modificação não
                        autorizados.</p>
                    <p>Não compartilhamos informações de identificação pessoal publicamente ou com terceiros, exceto quando
                        exigido por
                        lei.</p>
                    <p>O nosso site pode ter links para sites externos que não são operados por nós. Esteja ciente de que
                        não
                        temos controle sobre o conteúdo e práticas desses sites e não podemos aceitar responsabilidade por
                        suas
                        respectivas
                        <a href='https://privacidade.me/' target='_BLANK'>políticas de privacidade</a>.
                    </p>
                    <p>Você é livre para recusar a
                        nossa solicitação de informações pessoais, entendendo que talvez não possamos fornecer alguns dos
                        serviços
                        desejados.</p>
                    <p>O uso continuado de nosso site será considerado como aceitação de nossas práticas em torno de
                        privacidade e
                        informações pessoais. Se você tiver alguma dúvida sobre como lidamos com dados do usuário e
                        informações
                        pessoais, entre em contato conosco.</p>
                    <h2>Política de Cookies</h2>
                    <h3>O que são cookies?</h3>
                    <p>Como é prática comum em quase todos os sites profissionais, este site usa cookies, que são pequenos
                        arquivos
                        baixados no seu computador, para melhorar sua experiência. Esta página descreve quais informações
                        eles coletam,
                        como as usamos e por que às vezes precisamos armazenar esses cookies. Também compartilharemos como
                        você pode
                        impedir que esses cookies sejam armazenados, no entanto, isso pode fazer o downgrade ou 'quebrar'
                        certos
                        elementos da funcionalidade do site.</p>
                    <h3>Como usamos os cookies?</h3>
                    <p>Utilizamos cookies por vários motivos, detalhados abaixo. Infelizmente, na maioria dos casos, não
                        existem opções
                        padrão do setor para desativar os cookies sem desativar completamente a funcionalidade e os recursos
                        que eles
                        adicionam a este site. É recomendável que você deixe todos os cookies se não tiver certeza se
                        precisa ou não
                        deles, caso sejam usados ​​para fornecer um serviço que você usa.</p>
                    <h3>Desativar cookies</h3>
                    <p>Você pode impedir a configuração de cookies ajustando as configurações do seu navegador (consulte a
                        Ajuda do
                        navegador para saber como fazer isso). Esteja ciente de que a desativação de cookies afetará a
                        funcionalidade
                        deste e de muitos outros sites que você visita. A desativação de cookies geralmente resultará na
                        desativação de
                        determinadas funcionalidades e recursos deste site. Portanto, é recomendável que você não desative
                        os cookies.
                    </p>
                <h3>Cookies que definimos</h3>
                <ul>
                    <li> Cookies relacionados à conta<br><br> Se você criar uma conta conosco, usaremos cookies para o
                        gerenciamento
                        do processo de inscrição e administração geral. Esses cookies geralmente serão excluídos quando você
                        sair do
                        sistema, porém, em alguns casos, eles poderão permanecer posteriormente para lembrar as preferências
                        do seu
                        site ao sair.<br><br> </li>
                    <li> Cookies relacionados ao login<br><br> Utilizamos cookies quando você está logado, para que possamos
                        lembrar
                        dessa ação. Isso evita que você precise fazer login sempre que visitar uma nova página. Esses
                        cookies são
                        normalmente removidos ou limpos quando você efetua logout para garantir que você possa acessar
                        apenas a
                        recursos e áreas restritas ao efetuar login.<br><br> </li>
                    <li> Cookies relacionados a boletins por e-mail<br><br> Este site oferece serviços de assinatura de
                        boletim
                        informativo ou e-mail e os cookies podem ser usados ​​para lembrar se você já está registrado e se
                        deve
                        mostrar determinadas notificações válidas apenas para usuários inscritos / não inscritos.<br><br>
                    </li>
                    <li> Pedidos processando cookies relacionados<br><br> Este site oferece facilidades de comércio
                        eletrônico ou
                        pagamento e alguns cookies são essenciais para garantir que seu pedido seja lembrado entre as
                        páginas, para
                        que possamos processá-lo adequadamente.<br><br> </li>
                    <li> Cookies relacionados a pesquisas<br><br> Periodicamente, oferecemos pesquisas e questionários para
                        fornecer
                        informações interessantes, ferramentas úteis ou para entender nossa base de usuários com mais
                        precisão.
                        Essas pesquisas podem usar cookies para lembrar quem já participou numa pesquisa ou para fornecer
                        resultados
                        precisos após a alteração das páginas.<br><br> </li>
                    <li> Cookies relacionados a formulários<br><br> Quando você envia dados por meio de um formulário como
                        os
                        encontrados nas páginas de contacto ou nos formulários de comentários, os cookies podem ser
                        configurados
                        para lembrar os detalhes do usuário para correspondência futura.<br><br> </li>
                    <li> Cookies de preferências do site<br><br> Para proporcionar uma ótima experiência neste site,
                        fornecemos a
                        funcionalidade para definir suas preferências de como esse site é executado quando você o usa. Para
                        lembrar
                        suas preferências, precisamos definir cookies para que essas informações possam ser chamadas sempre
                        que você
                        interagir com uma página for afetada por suas preferências.<br> </li>
                </ul>
                <h3>Cookies de Terceiros</h3>
                <p>Em alguns casos especiais, também usamos cookies fornecidos por terceiros confiáveis. A seção a seguir
                    detalha
                    quais cookies de terceiros você pode encontrar através deste site.</p>
                <ul>
                    <li> Este site usa o Google Analytics, que é uma das soluções de análise mais difundidas e confiáveis
                        ​​da Web,
                        para nos ajudar a entender como você usa o site e como podemos melhorar sua experiência. Esses
                        cookies podem
                        rastrear itens como quanto tempo você gasta no site e as páginas visitadas, para que possamos
                        continuar
                        produzindo conteúdo atraente. </li>
                </ul>
                <p>Para mais informações sobre cookies do Google Analytics, consulte a página oficial do Google Analytics.
                </p>
                <ul>
                    <li> As análises de terceiros são usadas para rastrear e medir o uso deste site, para que possamos
                        continuar
                        produzindo conteúdo atrativo. Esses cookies podem rastrear itens como o tempo que você passa no site
                        ou as
                        páginas visitadas, o que nos ajuda a entender como podemos melhorar o site para você.</li>
                    <li> Periodicamente, testamos novos recursos e fazemos alterações subtis na maneira como o site se
                        apresenta.
                        Quando ainda estamos testando novos recursos, esses cookies podem ser usados ​​para garantir que
                        você receba.
</div>
                        <div class="content">
                            <h2><br>Termos de Uso</h2>
                            <h2>1. Termos</h2>
                            <p>Ao acessar ao site, concorda em cumprir
                                estes termos de
                                serviço, todas as leis e regulamentos aplicáveis ​​e concorda que é responsável pelo
                                cumprimento de todas as
                                leis locais aplicáveis. Se você não concordar com algum desses termos, está proibido de usar
                                ou acessar este
                                site. Os materiais contidos neste site são protegidos pelas leis de direitos autorais e
                                marcas comerciais
                                aplicáveis.</p>
                            <h3>2. Uso de Licença</h3>
                            <p>É concedida permissão para baixar temporariamente uma cópia dos materiais (informações ou
                                software) no site
                                HatchFy , apenas para visualização transitória pessoal e não comercial. Esta é a concessão
                                de uma licença, não
                                uma transferência de título e, sob esta licença, você não pode: </p>
                            <ol>
                                <li>modificar ou copiar os materiais; </li>
                                <li>usar os materiais para qualquer finalidade comercial ou para exibição pública (comercial
                                    ou não comercial);
                                </li>
                                <li>tentar descompilar ou fazer engenharia reversa de qualquer software contido no site
                                    HatchFy; </li>
                                <li>remover quaisquer direitos autorais ou outras notações de propriedade dos materiais; ou
                                </li>
                                <li>transferir os materiais para outra pessoa ou 'espelhe' os materiais em qualquer outro
                                    servidor.</li>
                            </ol>
                            <p>Esta licença será automaticamente rescindida se você violar alguma dessas restrições e poderá
                                ser rescindida por
                                HatchFy a qualquer momento. Ao encerrar a visualização desses materiais ou após o término
                                desta licença, você
                                deve apagar todos os materiais baixados em sua posse, seja em formato eletrónico ou
                                impresso.</p>
                            <h3>3. Isenção de responsabilidade</h3>
                            <ol>
                                <li>Os materiais no site da HatchFy são fornecidos 'como estão'. HatchFy não oferece
                                    garantias, expressas ou
                                    implícitas, e, por este meio, isenta e nega todas as outras garantias, incluindo, sem
                                    limitação, garantias
                                    implícitas ou condições de comercialização, adequação a um fim específico ou não
                                    violação de propriedade
                                    intelectual ou outra violação de direitos. </li>
                                <li>Além disso, o HatchFy não garante ou faz qualquer representação relativa à precisão, aos
                                    resultados
                                    prováveis ​​ou à confiabilidade do uso dos materiais em seu site ou de outra forma
                                    relacionado a esses
                                    materiais ou em sites vinculados a este site.</li>
                            </ol>
                            <h3>4. Limitações</h3>
                            <p>Em nenhum caso o HatchFy ou seus fornecedores serão responsáveis ​​por quaisquer danos
                                (incluindo, sem limitação,
                                danos por perda de dados ou lucro ou devido a interrupção dos negócios) decorrentes do uso
                                ou da incapacidade de
                                usar os materiais em HatchFy, mesmo que HatchFy ou um representante autorizado da HatchFy
                                tenha sido notificado
                                oralmente ou por escrito da possibilidade de tais danos. Como algumas jurisdições não
                                permitem limitações em
                                garantias implícitas, ou limitações de responsabilidade por danos conseqüentes ou
                                incidentais, essas limitações
                                podem não se aplicar a você.</p>
                            <h3>5. Precisão dos materiais</h3>
                            <p>Os materiais exibidos no site da HatchFy podem incluir erros técnicos, tipográficos ou
                                fotográficos. HatchFy não
                                garante que qualquer material em seu site seja preciso, completo ou atual. HatchFy pode
                                fazer alterações nos
                                materiais contidos em seu site a qualquer momento, sem aviso prévio. No entanto, HatchFy não
                                se compromete a
                                atualizar os materiais.</p>
                            <h3>6. Links</h3>
                            <p>O HatchFy não analisou todos os sites vinculados ao seu site e não é responsável pelo
                                conteúdo de nenhum site
                                vinculado. A inclusão de qualquer link não implica endosso por HatchFy do site. O uso de
                                qualquer site vinculado
                                é por conta e risco do usuário.</p>
                            <h3>Modificações</h3>
                            <p>O HatchFy pode revisar estes termos de serviço do site a qualquer momento, sem aviso prévio.
                                Ao usar este site,
                                você concorda em ficar vinculado à versão atual desses termos de serviço.</p>
                            <h3>Lei aplicável</h3>
                            <p>Estes termos e condições são regidos e interpretados de acordo com as leis do HatchFy e você
                                se submete
                                irrevogavelmente à jurisdição exclusiva dos tribunais naquele estado ou localidade.</p>
                        </div>
            </div>
        </section>
            <?php require "baseboard.php"?>
    </div>
    <script>
        var vue = new Vue({
            el: '#app',
            data: {
                isActiveBurger: false,
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger;
                },
            }
        })
    </script>
</body>

</html>
