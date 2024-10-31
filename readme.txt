=== 4all Plugin Woocommerce ===
Contributors: 4all, Thiago Siqueira
Tags: woocommerce, 4all, payment, pagamento, seguro, checkout, processamento, gateway, pag, transparente, pagar, receber
Requires at least: 4.0
Tested up to: 5.2
Stable tag: 2.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

4all gateway for WooCommerce.

== Descrição ==
Adicione a 4all como método de pagamento em sua loja WooCommerce.

A [4all](https://4all.com) é um gateway de pagamento brasileiro desenvolvido pela empresa de mesmo nome.

O plugin 4all for WooCommerce foi desenvolvido pela própria 4all com o intuito de permitir que e-commerces possam passar a usar a 4all em seus sites de maneira simples e sem muito trabalho.

A 4all para WooCommerce é um checkout direto que será adicionado na tela de pagamento para que seus clientes façam o pagamento diretamente da sua página, onde passam para nós as informações principais do pagamento.

= Compatibilidade =
Compatível desde a versão 2.3.x até 3.7.x do WooCommerce.

= Instalação =
Confira o nosso guia de instalação e configuração da 4all na aba [Installation](#installation).

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

* Criando um tópico no  [fórum de ajuda do WordPress](https://wordpress.org/support/plugin/pagamentos-digitais-4all).
* Criando um tópico no  [fórum do Github](https://github.com/4alltecnologia/plugin_woocommerce/issues).

= Colaborar =

Você pode contribuir com código-fonte em nossa página no [GitHub](https://github.com/4alltecnologia/plugin_woocommerce). Caso não saiba por onde começar, [aqui](https://www.youtube.com/watch?v=z8rLQsoUeHc) você pode encontrar um vídeo tutorial para ajuda-lo a instalar o Wordpress no seu **localhost**.

== Installation ==

= Instalação do plugin: =

* Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
* Ative o plugin.

= Requerimentos: =

* É necessário possuir uma conta na 4all.
* Ter instalado o plugin WooCommerce.

= Configurações do Plugin: =

Com o plugin instalado acesse o admin do WordPress e entre em "WooCommerce" > "Settings" > "Checkout", nessa página você encontrará duas opções, "4all - Cartão de crédito" e "4all - Boleto" entre em cada um, habilite e configure.

Preencha as opção de  **Chave do estabelicimento** com a merchantKey que você recebeu por e-mail após se cadastrar na 4all. Terão duas opções, uma na parte de integração, esta será a chave para acessar o ambiente de produção e a na parte de sandbox, esta será para acessar o servidor de homologação (testes).

Caso queira fazer alguns testes você pode ativar o modo de sandbox, este irá enviar todas as transações feitas pelo plugin pro ambiente de testes.

Pronto, sua loja já pode receber pagamentos pela 4all.