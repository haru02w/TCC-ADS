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
    <title>Quem Somos</title>
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
                <h1 class="title has-text-centered">Quem somos </h1>
                <p class="subtitle has-text-centered">Acreditamos ser transparentes e objetivos em nosso trabalho, visando buscar melhores maneiras
                    de recepcionar nossos usu??rios.
                </p>
                <div class="content">
                    <h1>Hist??ria</h1>
                    </p>
                    <p>Nosso grupo se formou quando houve necessidade de trabalharmos juntos na cria????o de um Trabalho de Conclus??o de Curso em 2021.
                        Tinhamos o objetivo de criar algo novo que pudesse ser capaz de ajudar diversas pessoas no meio digital, independentemente de
                        seu n??vel de experi??ncia com o mesmo, assim tornando nosso site acolhedor para todo p??blico.
                    </p>

                    <h1>Miss??o</h1>
                    <p>O intuito de nosso projeto ?? ajudar estudantes da ??rea de TI a conseguirem experi??ncia com problemas reais, lidar com
                        clientes, aprender tudo sobre como se portar no mercado ao mesmo tempo em que auxiliam pessoas em busca de uma solu????o
                        tecnol??gica para seus problemas, garantindo assim a forma????o de profissionais capacitados e ja com experi??ncia real.</p>

                    <h1>Vis??o</h1>
                    <p>Queremos que os novos profissionais que est??o se formando possam atuar no mercado sem problemas de inabilidade, sendo
                        completamente capazes de realizar qualquer pedido solicitado independente da dificuldade ou tempo necess??rio para faze-lo.</p>

                    <h1>Valores</h1>
                    <p>??? Responsabilidade<br />
                        ??? Confian??a<br />
                        ??? Honestidade<br />
                        ??? Seguran??a<br />
                        ??? Transpar??ncia<br />

                    </p>

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
