<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'878', // MerchantID
		'9485a72ed5fab4245f22ef97' // MerchantKey
	);
// Development
// $init->setPaymentURL('https://www.gudangvoucher.com/pg/v3/payment-sandbox.php');
// Production
$init->setPaymentURL('https://www.gudangvoucher.com/pg/v3/payment.php');
//
$init->setCallbackURL('https://a.g-dev.io/secure/callback/demo');
$init->setReturnURL('https://a.g-dev.io/secure/callback/demo');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
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
// $transaction->setPaymentMethod('bank_transfer,bca');
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('bank_transfer,bni');
// $transaction->setPaymentMethod('bank_transfer,cimb_niaga');
// $transaction->setPaymentMethod('bank_transfer,atm_bersama');
$transaction->setPaymentMethod('qris');

$vendor = new \Growinc\Payment\Vendors\GudangVoucher($init);

try {
	$result = $vendor->RedirectPayment($transaction);
	// $result = $vendor->SecurePayment($transaction);
	// extract($result);
	// print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
