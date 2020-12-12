<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('360360000000200', '4AC61F32A209A56B95712E0394E44AE620DD37ACD27C41AB64F4A99B22751420');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$transaction = new \Growinc\Payment\Transaction();

$transaction->setCardNumber($_REQUEST['setCardNumber'] ?? '4111111111111111');
$transaction->setCardExpMonth($_REQUEST['setCardExpMonth'] ?? '12');
$transaction->setCardExpYear($_REQUEST['setCardExpYear'] ?? '2020');
$transaction->setCardExpCvv($_REQUEST['setCardExpCvv'] ?? '984');

try {
	$enccarddata = $vendor->EncryptCardData($transaction);
	echo $enccarddata;
	/*
	00acGOy9DNhXqSk3bzIt0gLUpjCacQIn7Cz5wkoOpdKGBQW/B0w6kWBVp2RcpoCWb0yire4XlsUP8LG7TiE1SM+5SJOPGWNh5mByjiZm8jBRU2jFbEHZmvOJHcntgq/w2EdkUstqHaM4e/+Zwbl2uvCbl7+Qct+pLdZ54omKJeCVOpI=U2FsdGVkX1+J1KYmioWTmrzlzz6A4rVmZNerY2Y34DyAAttq71vA5xWlRDeXP7y+
	*/
} catch (\Throwable $e) {
	echo 'Encrypt card data failed: ' . $e->getMessage() . ':' . $e->getCode();
}
