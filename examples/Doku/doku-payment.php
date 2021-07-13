<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'11847610', // Store ID
		// '2FUyjWQWRwAS' // Shared Key Devel
		'XpbCOVxrLWby' // Shared Key Prod
	);
// $init->setPaymentURL('https://staging.doku.com/Suite/Receive');
$init->setPaymentURL('https://pay.doku.com/Suite/Receive');
$init->setRequestURL('http://103.5.45.182:15000/parse/'); // LIVE Server for parser

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('Lorem Ipsum');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081212345678');
$transaction->setCountrycode('360'); // ID
//
$transaction->setItem('Item Name');
$transaction->setAmount(rand(5000,10000) * 100);
$transaction->setDescription('Product Description');
//
// Payment Method Supported:
// 1. bank_transfer (VA) ----------- CURRENTLY ONLY THIS SUPPORTED
// 	bca
// 	permata
// 	bni
// 	cimb_niaga
// 	atm_bersama
// 2. qris
// 3. cstore (retail)
// 	alfamart
// 	indomaret
//
// $transaction->setPaymentMethod('va,danamon');
// $transaction->setPaymentMethod('va,bca');
$transaction->setPaymentMethod('va,permata');
// $transaction->setPaymentMethod('va,bni');
// $transaction->setPaymentMethod('va,cimb_niaga');
// $transaction->setPaymentMethod('va,atm_bersama');

$vendor = new \Growinc\Payment\Vendors\Doku($init);

try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}