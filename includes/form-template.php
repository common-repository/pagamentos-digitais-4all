<?php
  include_once 'woocommerce-4all-gateway.php';

  $gateway_4all = new woocommerce_4all_gateway($this->gatewaySettings);
  $paymentMethods = $gateway_4all->getPaymentMethods_4all();
  $nonePaymentMethods = false; //variavel para o caso do merchant ainda nao ter nenhuma affiliation cadastrada

  if ($paymentMethods["resume"]) {
    $brandsList = [];

    //a ordem das imagens esta de acordo com os id's retornados do gateway correspondendo a imagem
    $brands = [
      "https://4all.com/brands/visa.png", 
      "https://4all.com/brands/mastercard.png",
      "https://4all.com/brands/diners.png", 
      "https://4all.com/brands/elo.png", 
      "https://4all.com/brands/amex.png", 
      "https://4all.com/brands/discover.png", 
      "https://4all.com/brands/aura.png", 
      "https://4all.com/brands/jcb.png", 
      "https://4all.com/brands/hipercard.png"
    ];

    for ($i=0; $i < sizeof($paymentMethods["brands"]); $i++) { 
      //o -1 é necessario, pois o gateway retorna os id's de 1 para cima
      array_push($brandsList, $paymentMethods["brands"][$i]["brandId"] -1);
    }

    $brandsListString = implode(";", $brandsList);
  } else {
    $nonePaymentMethods = true;
  }
?>

<p class="form-row">
  <label><?=__('Name of the card holder (same as the card)', 'woocommerce-4all'); ?></label>
  <input type="text" name="cardholderName" maxlength="200">
</p>
<p class="form-row">
  <label><?=__('CPF of the bearer', 'woocommerce-4all'); ?></label>
  <input type="text" name="buyerDocument" maxlength="14">
</p>
<p class="form-row">
  <label><?=__('Card number', 'woocommerce-4all' ); ?></label>
  <input type="text" name="cardNumber" maxlength="19" <?php if ($nonePaymentMethods) { echo 'class="disabled" disabled'; } ?>>
</p>
<input type="hidden" id="brandsList" value="<?php echo $brandsListString; ?>">
<div class='form-row-brands'>
  <?php 
    if (!$nonePaymentMethods) {
      for ($i=0; $i < sizeof($brandsList); $i++) { 
        echo '<img src="' . $brands[$brandsList[$i]] . '" id="brand-' . $brandsList[$i] . '" class="">';
      }
    } else {
      echo '<p>'.__('There are no registered payment methods.', 'woocommerce-4all' ).'</p>';
    }
  ?>
</div>
<p class="form-row">
  <label><?=__('Expiration date', 'woocommerce-4all' ); ?></label>
  <input type="text" placeholder="MM/YY" name="expirationDate" maxlength="200">
</p>
<p class="form-row">
  <label><?=__('Security code', 'woocommerce-4all' ); ?></label>
  <input type="text" name="securityCode" maxlength="4">
</p>

<p class="form-row form-row-installment">
  <label><?=__('Installment', 'woocommerce-4all' ); ?></label>
  <select name="installment">

  <?php
    $minInstallment = $paymentMethods['resume']['minInstallments'];
    $maxInstallments = $paymentMethods['resume']['maxInstallments'];
    $total = WC()->cart->total;

    for (;$minInstallment<=$maxInstallments;$minInstallment++) {
      $value = $total / $minInstallment;
      $phrase = $minInstallment . 'x - R$ ';

      if ($minInstallment >= $this->interestInstallment && $this->interestInstallment > 0) {
        $interest = ($value * $this->interestRate) / 100;
        $value += $interest;
        $phrase = $phrase . number_format($value, 2, ',', '.');
        $phrase = $phrase . __(' interest of ', 'woocommerce-4all');
        $phrase = $phrase . number_format($interest, 2, ',', '.');
      } else {
        $phrase = $phrase . number_format($value, 2, ',', '.');
      }

      echo '<option value="'.$minInstallment.'">'.$phrase.'</option>';
    }
  ?>
  </select>
</p>
<p><?= __('&#9679; Approximated values', 'woocommerce-4all'); ?></p>
