<?php require_once('./config.php'); 

/*$makeplan = \Stripe\Plan::create(array(
  "amount" => 55000,
  "interval" => "month",
  "name" => "Terra Zoel - Platinum",
  "currency" => "usd",
  "id" => "terra_zoel_platinum")
);
print_r($makeplan);*/

$listplans = \Stripe\Plan::retrieve("terra_zoel_platinum");//\Stripe\Plan::all();
echo "<pre>";
print_r($listplans);
?>

<!--<form action="charge.php" method="post">
  <script src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
          data-key="<?php echo $stripe['publishable_key']; ?>"
          data-amount="5000" data-description="One year's subscription"></script>
</form>-->