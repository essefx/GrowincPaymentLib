<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y', // secret_key
		'xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy' // public_key
	);
$init->setBaseURI('https://api.xendit.co');
$init->setPaymentURL('https://api.xendit.co');

/*------------------------------ V V V Start of Required by EWALLET and QRIS ---------- */
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');
/*------------------------------ A A A End of Required by EWALLET and QRIS ---------- */

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
$transaction->setCountrycode('IDN');
//
$transaction->setItem('Apple');
$transaction->setAmount(100000);
$transaction->setDescription('Pembelian Elektronik');

// Payment Method Supported:
// 1. bank_transfer (VA) ----------- CURRENTLY ONLY THIS SUPPORTED
// 	bca
// 	bni
// 	bri
// 	mandiri
// 	permata
// 	sahabat_sampoerna
// 2. credit_card
// 3. ewallet
// 	ovo
// 	linkaja
// 	shopeepay
// 3. qr (QRIS)
// 4. cstore (retail)
// 	alfamart
// 	indomaret

// $transaction->setPaymentMethod('bank_transfer,bca');
// $transaction->setPaymentMethod('bank_transfer,mandiri');
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('credit_card'); // Currently Inapplicable
// $transaction->setPaymentMethod('ewallet,ovo');
// $transaction->setPaymentMethod('ewallet,linkaja');
$transaction->setPaymentMethod('qris');
// $transaction->setPaymentMethod('cstore,indomaret');
// $transaction->setPaymentMethod('cstore,alfamart');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	// extract($result);
	// print_r($response);
	print_r($result);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getCode();
}
