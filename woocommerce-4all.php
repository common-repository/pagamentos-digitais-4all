<?php
  /**
   * Plugin Name: Pagamentos Digitais 4all
   * Plugin URI:  https://github.com/4alltecnologia/plugin_woocommerce.git
   * Description: Includes 4all as a payment gateway to WooCommerce.
   * Author:      4all, Thiago Siqueira
   * Version:     2.1.2
   * License:     GPLv2 or later
   * Text Domain: pagamentos-digitais-4all
   * Domain Path: /languages
   *
   * 4all is free software: you can redistribute it and/or modify
   * it under the terms of the GNU General Public License as published by
   * the Free Software Foundation, either version 2 of the License, or
   * any later version.
   *
   * 4all is distributed in the hope that it will be useful,
   * but WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   * GNU General Public License for more details.
   *
   * You should have received a copy of the GNU General Public License
   * along with 4all. If not, see
   * <https://www.gnu.org/licenses/gpl-2.0.txt>.
   *
   * @package WooCommerce_4all
   */
  add_action('plugins_loaded', 'woocommerce_4all_init');

  function woocommerce_4all_init()
  {
    load_plugin_textdomain('woocommerce-4all', false, dirname(plugin_basename( __FILE__ )) . '/languages/');

    if (!class_exists('WC_Payment_Gateway')) return;

    include_once('includes/class-woocommerce-4all.php');
    include_once('includes/class-woocommerce-bank-slip-4all.php');
  }

  add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'plugin_action_links' );

  function plugin_action_links( $links ) {
    $plugin_links   = array();
    $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=4all' ) . '">' . __( 'Credit card settings', 'woocommerce-4all' ) . '</a>';
    $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=4all-bank-slip' ) . '">' . __( 'Bank slip settings', 'woocommerce-4all' ) . '</a>';

    return array_merge( $plugin_links, $links );
	}

  function woocommerce_4all_add_gateway($methods)
  {
    $methods[] = 'WC_Gateway_4all';
    $methods[] = 'WC_Bank_Slip_4all';
    return $methods;
  }

  function woocommerce_4all_add_css() {
    $basePluginName = plugin_basename(plugin_dir_path( __FILE__ ));
    $styleUrl = plugins_url( $basePluginName . '/assets/css/frontend/4all-style.css');
    wp_enqueue_style( 'woocommerce_4all_style', $styleUrl);
  }

  function woocommerce_4all_add_js() {
    $basePluginName = plugin_basename(plugin_dir_path( __FILE__ ));
    $scriptUrl = plugins_url( $basePluginName . '/assets/js/4all-scripts.js');
    wp_enqueue_script( 'woocommerce_4all_script', $scriptUrl, array('jquery'));
  }

  add_action( 'wp_enqueue_scripts', 'woocommerce_4all_add_css' );
  add_action( 'wp_footer', 'woocommerce_4all_add_js' );

  add_filter('woocommerce_payment_gateways', 'woocommerce_4all_add_gateway');
