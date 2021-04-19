<?php

require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6');
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setPaymentURL('https://sandbox-api.espay.id/rest/digitalpay/pushtopay');

$transaction = new \Growinc\Payment\Transaction();

// $transaction->setRuuid('123A-DEF4-1214');
// $transaction->setTime('2020-11-07 11:17:45'); //
// $transaction->setCommcode('SGWGROWINC');
// $transaction->setOrderID('21315');
// $transaction->setAmount('20000.00');
// $transaction->setSignatureKey('ces0bu1jh9qrsakq');
// $transaction->setDescription();
// $transaction->setProductCode('LINKAJA');
$transaction->setPaymentMethod('e_wallet,link_aja');
$transaction->setCustomerUserid('081111504410');
$transaction->setPromoCode(''); // promo code
$transaction->setIsAsync(1); // 1 = Sync / 0 = Async Default Async
$transaction->setBranchId(''); // optional
$transaction->setPostId(''); // optional
$transaction->setCredentialAttr('ces0bu1jh9qrsakq//Y0F,(5EM=#//SGWGROWINC//PUSHTOPAY');

$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setCountrycode('IDN');

$item_detail = [
	[
		"id" => "mi-b2",
		"price" => 90000,
		"quantity" => '2',
		"name" => "Poco M3",
		"brand" => "Xiaomi",
		"category" => "Smartphone",
		"merchant_name" => "Grand Line Store"
	]
];

$transaction->setItem($item_detail);
// $transaction->setDescription('Pembelian Elektronik');

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
	$result = $vendor->SecurePaymentWallet($transaction);
	extract($result);
	print_r($result);
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' .  $e->getCode();
}
