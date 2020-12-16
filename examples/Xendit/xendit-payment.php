<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y', // secret_key
		'xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy' // public_key
	);
$init->setBaseURI('https://api.xendit.co');
$init->setPaymentURL('https://api.xendit.co');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
$transaction->setAmount(100000);
$transaction->setCountrycode('IDN');

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

$transaction->setPaymentMethod('bank_transfer,bca');
// $transaction->setPaymentMethod('bank_transfer,mandiri');
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('credit_card');
// $transaction->setPaymentMethod('ewallet,ovo');
// $transaction->setPaymentMethod('ewallet,linkaja');
// $transaction->setPaymentMethod('qris');
// $transaction->setPaymentMethod('cstore,indomaret');
// $transaction->setPaymentMethod('cstore,alfamart');

$transaction->setDescription('Pembelian Elektronik');

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
