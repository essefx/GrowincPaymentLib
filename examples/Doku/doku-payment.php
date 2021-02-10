<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'11847610', // Store ID
		'2FUyjWQWRwAS' // Shared Key
	);
$init->setPaymentURL('https://staging.doku.com/Suite/Receive');

/*------------------------------ V V V Start of Required by EWALLET and QRIS ---------- */
// $init->setCallbackURL('https://a.g-dev.io/callback');
// $init->setReturnURL('https://a.g-dev.io/callback');
$init->setCallbackURL('https://a.g-dev.io/secure/callback/demo');
$init->setReturnURL('https://a.g-dev.io/secure/callback/demo');
/*------------------------------ A A A End of Required by EWALLET and QRIS ---------- */

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
$transaction->setCountrycode('IDN');
//
$transaction->setItem('Apple');
$transaction->setAmount(rand(5000,10000) * 100);
$transaction->setDescription('Pembelian Elektronik');
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
$transaction->setPaymentMethod('bank_transfer,bca');
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('bank_transfer,bni');
// $transaction->setPaymentMethod('bank_transfer,cimb_niaga');
// $transaction->setPaymentMethod('bank_transfer,atm_bersama');

$vendor = new \Growinc\Payment\Vendors\Doku($init);

// try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
// } catch (\Throwable $e) {
// 	echo 'Payment failed: ' . $e->getCode();
// }
