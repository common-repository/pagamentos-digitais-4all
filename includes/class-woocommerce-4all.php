<?php

  include_once 'woocommerce-4all-gateway.php';

  class WC_Gateway_4all extends WC_Payment_Gateway
    {
      function __construct()
      {
        $this->id = '4all';
        $this->has_fields = true;
        $this->method_title = __('4all - Credit Card', 'woocommerce-4all');
        $this->method_description = __('Official 4all payment gateway for WooCommerce.', 'woocommerce-4all');

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->settings['title'];
        $this->enabled = $this->settings['enabled'];
        $this->description = $this->settings['description'];
        $this->interestInstallment = (int) $this->settings['interestInstallment'];
        $this->interestRate = (float) number_format(
            preg_replace(
              '/([^.0-9])/',
              '',
              str_replace(
                ',',
                '.',
                sanitize_text_field($this->settings['interestRate'])
              )
            ),
            2
        );
        $this->gatewaySettings = [
          "merchantKey" => $this->settings['enabledHomolog'] == 'yes' ? $this->settings['homologMerchantKey'] : $this->settings['merchantKey'],
          "environment" => $this->settings['enabledHomolog'] == 'yes' ? 'https://gateway.homolog-interna.4all.com/' : 'https://gateway.api.4all.com/',
          "paymentMode" => 1
        ];

        if ($this->settings['logo'] == 'yes') {
          $this->icon = apply_filters( 'wc_gateway_4all_icon', plugins_url( 'assets/images/PAGAMENTOS-DIGITAIS.png', plugin_dir_path( __FILE__ ) ) );
        }

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
      }

      /**
      * Returns a value indicating the the Gateway is available or not. It's called
      * automatically by WooCommerce before allowing customers to use the gateway
      * for payment.
      *
      * @return bool
      */
      public function is_available() {
        $available = 'yes' == $this->get_option( 'enabled' );

        return $available;
      }

      /**
      * Payment fields.
      */
      public function payment_fields() {
        include('form-template.php');
      }

      public function validate_fields()
      {
        $valid = true;

        if (empty( $_REQUEST['cardholderName'] )) {
          wc_add_notice( '<strong>"' . __('Card name', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (!preg_match('/([A-z])/', $_REQUEST['cardholderName']) || strlen($_REQUEST['cardholderName']) < 2 
        || strlen($_REQUEST['cardholderName']) > 28){
          wc_add_notice( '<strong>"' . __('Card name', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        if (empty( $_REQUEST['cardNumber'] )) {
          wc_add_notice( '<strong>"' . __('Card number', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['cardNumber']) || strlen($_REQUEST['cardNumber']) < 12 
        || strlen($_REQUEST['cardNumber']) > 19) {
          wc_add_notice( '<strong>"' . __('Card number', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        if (empty( $_REQUEST['buyerDocument'] )) {
          wc_add_notice( '<strong>"' . __('CPF', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['buyerDocument']) || strlen($_REQUEST['buyerDocument']) < 14 
        || strlen($_REQUEST['buyerDocument']) > 14) {
          wc_add_notice( '<strong>"' . __('CPF', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        if (empty( $_REQUEST['expirationDate'] )) {
          wc_add_notice( '<strong>"' . __('Expiration date', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (!preg_match('/([0-1]{1}[0-9]{1}[\/]{1}[0-9])/', $_REQUEST['expirationDate']) || strlen($_REQUEST['expirationDate']) != 5) {
          wc_add_notice( '<strong>"' . __('Expiration date', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        if (empty( $_REQUEST['securityCode'] )) {
          wc_add_notice( '<strong>"' . __('Security code', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['securityCode']) || strlen($_REQUEST['securityCode']) < 3 
        || strlen($_REQUEST['securityCode']) > 4) {
          wc_add_notice( '<strong>"' . __('Security code', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        return $valid;
      }

      /**
      * Initialize Gateway Settings Form Fields
      **/
      public function init_form_fields() { 
        $this->form_fields = apply_filters( 'wc_gateway_4all_form_fields', array(
              
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'woocommerce-4all' ),
                'type'    => 'checkbox',
                'label'   => ' ',
                'description' => __('If you do not already have 4all merchant account, <a href="https://autocredenciamento.4all.com" target="_blank">please register in Production</a>', 'woocommerce-4all'),
                'default' => 'yes'
            ),

            'logo' => array(
                'title'   => __( 'Show image', 'woocommerce-4all' ),
                'type'    => 'checkbox',
                'label'   => ' ',
                'description' => __('When active show "4all pagamentos digitais" logo at checkout page', 'woocommerce-4all'),
                'default' => 'no'
            ),

            'title' => array(
              'title'       => __( 'Title', 'woocommerce-4all' ),
              'type'        => 'text',
              'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-4all' ),
              'desc_tip'    => true,
              'default'     => __( 'Credit card', 'woocommerce-4all' ),
            ),

            'description' => array(
                'title'       => __( 'Description:', 'woocommerce-4all' ),
                'type'        => 'textarea',
                'description' => __( 'Description of 4all Payment Gateway that users sees on Checkout page.', 'woocommerce-4all' ),
                'default'     => __( '4all is a leading payment services provider.', 'woocommerce-4all' ),
                'desc_tip'    => true,
            ),

            'integration' => array(
                'title'       => __( 'Integration Settings', 'woocommerce-4all' ),
                'type'        => 'title',
                'description' => '',
            ),

            'merchantKey' => array(
              'title'             => __( 'MerchantKey', 'woocommerce-4all' ),
              'type'              => 'text',
              'description'       => __( 'Please enter your 4all MerchantKey. This is needed to process the payment.', 'woocommerce-4all' ),
              'default'           => '',
              'custom_attributes' => array(
                'required' => 'required',
              ),
            ),

            'interest' => array(
                'title'       => __( 'Interest Settings', 'woocommerce-4all' ),
                'type'        => 'title',
                'description' => '',
            ),

            'interestInstallment' => array(
              'title'             => __( 'Interest installments', 'woocommerce-4all' ),
              'type'              => 'select',
              'description'       => __( 'From how many installments the interest will be applied.', 'woocommerce-4all' ),
              'desc_tip'    => true,
              'default'           => '0',
              'options' => array(
                '0' => __( 'None', 'woocommerce-4all' ),
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10',
                '11' => '11',
                '12' => '12',
              )
            ),

            'interestRate' => array(
              'title'             => __( 'Interest rate', 'woocommerce-4all' ),
              'type'              => 'text',
              'description'       => __( 'Percentage interest rate that will be applied in the installments. Example: 1.10', 'woocommerce-4all' ),
              'desc_tip'          => true,
              'default'           => '0',
            ),

            'sandbox' => array(
                'title'       => __( 'Sandbox Settings', 'woocommerce-4all' ),
                'type'        => 'title',
                'description' => '',
            ),

            'enabledHomolog' => array(
              'title'   => __( 'Enable/Disable', 'woocommerce-4all' ),
                'type'    => 'checkbox',
                'label'   => ' ',
                'description' => __('When active send all transactions to homolog server', 'woocommerce-4all'),
                'default' => 'no'
            ),

            'homologMerchantKey' => array(
              'title'             => __( 'MerchantKey', 'woocommerce-4all' ),
              'type'              => 'text',
              'description'       => __( 'Please enter your merchantKey of homolog. This is needed to process the payment.', 'woocommerce-4all' ),
              'default'           => '',
            ),

        ) );
      } // close init_form_fields

      public function add_customer_4all(){

        $data = [];

        if ($_REQUEST["billing_first_name"] && $_REQUEST["billing_last_name"]) {
          $fullName = $_REQUEST["billing_first_name"] . ' ' . $_REQUEST["billing_last_name"];
          $data["fullName"] = sanitize_text_field($fullName);
        }

        if ($_REQUEST["billing_address_1"]) {
          $data["address"] = sanitize_text_field($_REQUEST["billing_address_1"]);
        }

        if ($_REQUEST["billing_city"]) {
          $data["city"] = sanitize_text_field($_REQUEST["billing_city"]);
        }

        if ($_REQUEST["billing_state"]) {
          $data["state"] = sanitize_text_field($_REQUEST["billing_state"]);
        }

        if ($_REQUEST["billing_postcode"]) {
          $data["zipCode"] = sanitize_text_field($_REQUEST["billing_postcode"]);
        }

        if ($_REQUEST["billing_phone"]) {
          $data["phoneNumber"] = sanitize_text_field($_REQUEST["billing_phone"]);
        }

        if ($_REQUEST["billing_email"]) {
          $data["emailAddress"] = sanitize_email($_REQUEST["billing_email"]);
        }

        if (sizeof($data) > 0 ) {
          return $data;
        }

        return null;
      }

      /*
      * Try make the payment
      */
      public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        $gateway_4all = new woocommerce_4all_gateway($this->gatewaySettings);
        $metaData = [
          "cardData" => [
            "cardholderName" => sanitize_text_field($_REQUEST["cardholderName"]),
            "buyerDocument" => sanitize_text_field($_REQUEST["buyerDocument"]),
            "cardNumber" => str_replace(' ', '', sanitize_text_field($_REQUEST["cardNumber"])),
            "expirationDate" => str_replace('/', '', sanitize_text_field($_REQUEST["expirationDate"])),
            "securityCode" => sanitize_text_field($_REQUEST["securityCode"])
          ],
          "installments" => (int)sanitize_text_field($_REQUEST['installment']),
          "total" => round($order->get_total() * 100),
          "metaId" => "" . $order_id
        ];

        if ($this->interestInstallment > 0 && $this->interestRate > 0 && $metaData['installments'] >= $this->interestInstallment) {
          $metaData["interestRules"] = [[
            "min" => $this->interestInstallment,
            "max" => 1 + $metaData['installments'], // Precaucao, pois o max nao pode ser igual ao minimo
            "percentual" => $this->interestRate,
          ]];
        }

        $findToReplace = array('.', '-');
        $metaData["customer"] = $this->add_customer_4all();
        $metaData["customer"]["cpf"] = str_replace($findToReplace, '', sanitize_text_field($_REQUEST["buyerDocument"]));

        $tryPay = $gateway_4all->paymentFlow_4all($metaData);

        if ($tryPay["error"]) {
          wc_add_notice( __('Payment error: ', 'woothemes') . __($tryPay["error"]["message"], 'woocommerce-4all'), 'error' );
          return;
        }

        $payment_data = array(
          "paymentId" => $tryPay["transactionId"],
          "payment_status" => $tryPay["status"],
        );

        update_post_meta( $order_id, '_wc_4all_payment_data', $payment_data );
        
        // Payment complete
        $order->payment_complete();
                
        // Return thank you redirect
        return array(
            'result'    => 'success',
            'redirect'  => $this->get_return_url( $order )
        );
      } // process_payment

    } // close class