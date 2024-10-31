module.exports = function (grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    pot: {
      options: {
        text_domain: 'woocommerce-4all', //Your text domain. Produces my-text-domain.pot
        dest: 'languages/', //directory to place the pot file
        keywords: ['xgettext', '__'], //functions to look for
        encoding: 'UTF-8'
      },
      files: {
        src: ['**/*.php'], //Parse all php files
        expand: true,
      }
    }
  });

  /*
    OBSERVAÇÃO: Esse script não adiciona as informações sobre o plugin e o autor contidas no arquivo woocommerce-4all.php na raiz do 
    projeto, porém o wordpress as entende como informações que devem ser traduzidas, então quando gerar um novo .pot a partir desse 
    script é necessario adicionar ao .pot as seguintes informações:

    #. Author of the plugin/theme
    msgid "4all, Thiago Siqueira"
    msgstr ""

    #. Description of the plugin/theme
    msgid "Includes 4all as a payment gateway to WooCommerce."
    msgstr ""

    #. Plugin URI of the plugin/theme
    msgid "https://github.com/4alltecnologia/plugin_woocommerce.git"
    msgstr ""

    #. Plugin Name of the plugin/theme
    msgid "Pagamentos Digitais 4all"
    msgstr ""
  */

  grunt.loadNpmTasks('grunt-pot');

  grunt.registerTask('default', ['pot']);
};