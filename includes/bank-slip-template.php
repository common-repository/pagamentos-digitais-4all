<label class="title"><?= __('Buyer complement', 'woocommerce-4all'); ?></label>
<br>
<p class="form-row">
  <label><?=__('CPF of the buyer', 'woocommerce-4all'); ?></label>
  <input type="text" name="buyerDocumentBankSlip" maxlength="14">
</p>
<label class="title"><?= __('Address complement', 'woocommerce-4all'); ?></label>
<br>
<p class="form-row">
  <label><?=__('Residence neighborhood', 'woocommerce-4all'); ?></label>
  <input type="text" name="addressNeighborhood" maxlength="200">
</p>
<p class="form-row">
  <label><?=__('Residence number', 'woocommerce-4all'); ?></label>
  <input type="text" name="addressNumber" maxlength="8">
</p>
<?php
  $description = $this->get_description();

  if (strlen($description) > 0) {
    echo '<p>' . $description . '</p>';
  } else {
    echo '<p>' . __('After you finish your purchase, you will have access to the bank slip that you can print and pay. It can take up to 45 minutes to be available to be paid.', 'woocommerce-4all') . '</p>';
  }
?>
