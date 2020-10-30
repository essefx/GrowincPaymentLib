<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('D6677', '9180265c1850e3ec2286f3b139d4c260');
// $init->setBaseURI('https://sandbox.duitku.com/webapi/api/merchant');
$init->setPaymentURL('https://sandbox.duitku.com/webapi/api/merchant');
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('SEAN');
$transaction->setCustomerEmail('essefx@gmail.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setAmount(100000);
// VC	Credit Card (Visa / Master)
// BK	BCA KlikPay
// M1	Mandiri Virtual Account
// BT	Permata Bank Virtual Account
// A1	ATM Bersama
// B1	CIMB Niaga Virtual Account
// I1	BNI Virtual Account
// VA	Maybank Virtual Account
// FT	Ritel
// OV	OVO
// DN	Indodana Paylater
// SP	Shopee Pay
// SA	Shopee Pay Apps
// AG	Bank Artha Graha
// S1	Bank Sahabat Sampoerna
$transaction->setPaymentMethod('B1');

$vendor = new \Growinc\Payment\Vendors\Duitku($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	// $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
	extract($result);
	print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()->getBody()->getContents()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
