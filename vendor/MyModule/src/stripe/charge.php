<?php
  require_once('./config.php');

  $token  = $_POST['stripeToken'];

  $customer = \Stripe\Customer::create(array(
      'email' => 'abc@abc.com',
      'card'  => $token
  ));

  $charge = \Stripe\Charge::create(array(
      'customer' => $customer->id,
      'amount'   => 5000,
      'currency' => 'usd'
  ));

print_r($charge);
  echo '<h1>Successfully charged $50!</h1>';
?>