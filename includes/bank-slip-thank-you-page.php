<div class="woocommerce-message">
	<span>
    <?php echo __( 'Please click in the following link to view your bank slip: ', 'woocommerce-4all' ); ?>
    <a href="<?php echo esc_url( $paymentSlipUrl ); ?>" target="_blank"><?= __( 'Access bank slip', 'woocommerce-4all' ); ?></a>
    <br>
    <?php echo __('You can print and pay in your internet banking or in a lottery retailer. Remember, it can take up to 45 minutes to be available to be paid.', 'woocommerce-4all'); ?>
    <br>
    <?php echo __( 'After we receive the banking ticket payment confirmation, your order will be processed.', 'woocommerce-4all' ); ?>
  </span>
</div>