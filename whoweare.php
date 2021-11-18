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
                    de recepcionar nossos usuários.
                </p>
                <div class="content">
                    <h1>História</h1>
                    </p>
                    <p>Nosso grupo se formou quando houve necessidade de trabalharmos juntos na criação de um Trabalho de Conclusão de Curso em 2021.
                        Tinhamos o objetivo de criar algo novo que pudesse ser capaz de ajudar diversas pessoas no meio digital, independentemente de
                        seu nível de experiência com o mesmo, assim tornando nosso site acolhedor para todo público.
                    </p>

                    <h1>Missão</h1>
                    <p>O intuito de nosso projeto é ajudar estudantes da área de TI a conseguirem experiência com problemas reais, lidar com
                        clientes, aprender tudo sobre como se portar no mercado ao mesmo tempo em que auxiliam pessoas em busca de uma solução
                        tecnológica para seus problemas, garantindo assim a formação de profissionais capacitados e ja com experiência real.</p>

                    <h1>Visão</h1>
                    <p>Queremos que os novos profissionais que estão se formando possam atuar no mercado sem problemas de inabilidade, sendo
                        completamente capazes de realizar qualquer pedido solicitado independente da dificuldade ou tempo necessário para faze-lo.</p>

                    <h1>Valores</h1>
                    <p>• Responsabilidade<br />
                        • Confiança<br />
                        • Honestidade<br />
                        • Segurança<br />
                        • Transparência<br />

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
