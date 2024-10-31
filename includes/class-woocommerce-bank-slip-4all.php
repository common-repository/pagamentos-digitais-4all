<?php

  include_once 'woocommerce-4all-gateway.php';

  class WC_Bank_Slip_4all extends WC_Payment_Gateway
    {
      function __construct()
      {
        $this->id = '4all-bank-slip';
        $this->has_fields = true;
        $this->method_title = __('4all - Bank slip', 'woocommerce-4all');
        $this->method_description = __('Official 4all payment gateway for WooCommerce.', 'woocommerce-4all');

        $this->init_form_fields();
        $this->init_settings();

        $this->title = $this->settings['title'];
        $this->enabled = $this->settings['enabled'];
        $this->description = $this->settings['description'];
        $this->dueDate = $this->settings['dueDate'];
        $this->daysToExpire = $this->settings['daysToExpire'];
        $this->gatewaySettings = [
          "merchantKey" => $this->settings['enabledHomolog'] == 'yes' ? $this->settings['homologMerchantKey'] : $this->settings['merchantKey'],
          "environment" => $this->settings['enabledHomolog'] == 'yes' ? 'https://gateway.homolog-interna.4all.com/' : 'https://gateway.api.4all.com/',
          "paymentMode" => 3
        ];

        $this->paymentSlip = [
          "observations" => [__('This bank slip can take up to 45 minutes after it is created to be registered and then be available paid', 'woocommerce-4all')],
          "dueDate" => date('Y-m-d', strtotime(date() . " + " . $this->dueDate ." day")),
          "daysToExpire" => (int)$this->settings['daysToExpire']
        ];

        if ($this->settings['logo'] == 'yes') {
          $this->icon = apply_filters( 'wc_gateway_4all_icon', plugins_url( 'assets/images/PAGAMENTOS-DIGITAIS.png', plugin_dir_path( __FILE__ ) ) );
        }

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
      }

      public function is_available() {
        $available = 'yes' == $this->get_option( 'enabled' );

        return $available;
      }

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
              'default'     => __( 'Bank slip', 'woocommerce-4all' ),
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
              'description'       => __( 'Please enter your 4all MerchantKey of production. This is needed to process the payment.', 'woocommerce-4all' ),
              'default'           => '',
              'custom_attributes' => array(
                'required' => 'required',
              ),
            ),

            'dueDate' => array(
              'title'             => __( 'Days to due date', 'woocommerce-4all' ),
              'type'              => 'text',
              'description'       => __( 'Days until to bank slip due date', 'woocommerce-4all' ),
              'default'           => '7',
              'custom_attributes' => array(
                'required' => 'required',
              ),
            ),

            'daysToExpire' => array(
              'title'             => __( 'Days to expire', 'woocommerce-4all' ),
              'type'              => 'select',
              'description'       => __( 'Days until to bank slip expire', 'woocommerce-4all' ),
              'default'           => '15',
              'options' => array(
                '15' => '15',
                '30' => '30'
              )
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
      }

      public function payment_fields() {
        include('bank-slip-template.php');
      }

      public function validate_fields()
      {
        $valid = true;

        if (empty( $_REQUEST['buyerDocumentBankSlip'] )) {
          wc_add_notice( '<strong>"' . __('CPF', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['buyerDocumentBankSlip']) || strlen($_REQUEST['buyerDocumentBankSlip']) < 14 
        || strlen($_REQUEST['buyerDocumentBankSlip']) > 14) {
          wc_add_notice( '<strong>"' . __('CPF', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        if (empty( $_REQUEST['addressNeighborhood'] )) {
          wc_add_notice( '<strong>"' . __('Neighborhood', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (strlen($_REQUEST['addressNeighborhood']) < 1 || strlen($_REQUEST['addressNeighborhood']) > 200) {
          wc_add_notice( '<strong>"' . __('Neighborhood', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        if (empty( $_REQUEST['addressNumber'] )) {
          wc_add_notice( '<strong>"' . __('Number', 'woocommerce-4all') . '"</strong> ' . __( 'is a required field.', 'woocommerce-4all' ), 'error' );

          $valid = false;
        } elseif (!preg_match('/([0-9])/', $_REQUEST['addressNumber']) || strlen($_REQUEST['addressNumber']) > 8) {
          wc_add_notice( '<strong>"' . __('Number', 'woocommerce-4all') . '"</strong> ' . __( 'is not a valide value.', 'woocommerce-4all' ), 'error' );
          
          $valid = false;
        }

        return $valid;
      }

      public function add_customer_4all(){
        $data = [];

        if ($_REQUEST["billing_first_name"] && $_REQUEST["billing_last_name"]) {
          $fullName = sanitize_text_field($_REQUEST["billing_first_name"]) . ' ' . sanitize_text_field($_REQUEST["billing_last_name"]);
          $data["fullName"] = $fullName;
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
          $data["zipCode"] = sanitize_text_field(str_replace(array('.', '-'), '', $_REQUEST["billing_postcode"]));
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

      public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        $gateway_4all = new woocommerce_4all_gateway($this->gatewaySettings);

        $metaData = [
          "total" => round($order->get_total() * 100),
          "metaId" => "" . $order_id,
          "customer" => $this->add_customer_4all(),
          "paymentSlip" => $this->paymentSlip
        ];

        $findToReplace = array('.', '-');
        $metaData["customer"]["cpf"] = sanitize_text_field(str_replace($findToReplace, '', $_REQUEST["buyerDocumentBankSlip"]));
        $metaData["customer"]["neighborhood"] = sanitize_text_field($_REQUEST["addressNeighborhood"]);
        $metaData["customer"]["number"] = sanitize_text_field($_REQUEST["addressNumber"]);

        $tryPay = $gateway_4all->paymentFlow_4all($metaData);

        if ($tryPay["error"]) {
          wc_add_notice( __('Payment error: ', 'woothemes') . __($tryPay["error"]["message"], 'woocommerce-4all'), 'error' );
          return;
        }

        $uuid = $tryPay["paymentMethods"][0]["paymentSlip"]["uuid"];
        $bankSlipUrl = $this->gatewaySettings['environment'] . "view/paymentSlip?uuid=" . $uuid . "&format=pdf";

        $payment_data = array(
          "paymentId" => $tryPay["transactionId"],
          "payment_status" => $tryPay["status"],
          "bankSlipUrl" => $bankSlipUrl,
        );

        update_post_meta( $order_id, '_wc_4all_payment_data', $payment_data );

        $order->update_status('on-hold', __('Awaiting bank slip payment', 'woocommerce-4all'));

        return array(
            'result'    => 'success',
            'redirect'  => $this->get_return_url( $order )
        );
      }

      public function thankyou_page( $order_id ) {
        $paymentData  = get_post_meta( $order_id, '_wc_4all_payment_data', true );
        $paymentSlipUrl = $paymentData["bankSlipUrl"];

        include('bank-slip-thank-you-page.php');
      }
    }