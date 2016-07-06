<?php
require './stripephp340/init.php';

$stripe['secret_key'] = ('sk_test_BARnnKEY9MkfDlqxd0T7uWbz');
$stripe['publishable_key'] = ('pk_test_9NBCSVANaEpD9Tulpl1bQLql');


\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>