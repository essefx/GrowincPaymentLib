<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$private_key_1 = '000f1f4cb5118390cc2ec79af671d617';
$private_key_2 = '19c6f7a74281b16c2e70ba485dcf1750';
$init = new \Growinc\Payment\Init(
		$private_key_1,
		$private_key_2
	);

$merchant_key = 'a85b54a715b31119a654928c400c8bb8';
$init->setMerchantKey($merchant_key);

$init->setRequestURL('https://secure-payment.winpay.id/transaction'); // Production URL
// $init->setRequestURL('https://sandbox-payment.winpay.id/transaction'); // Development URL

$vendor = new \Growinc\Payment\Vendors\Winpay($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '0014244938',
			'id_transaction' => '', // Optional
		]);
	extract($result);
	print_r($response);
	// Success
	/*
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
