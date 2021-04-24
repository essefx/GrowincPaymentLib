<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// DEV
// $init = new \Growinc\Payment\Init('D8014', '28231019d86b773f94d30f503bd041c1'); // DuitkuGTIDev
// $init->setBaseURI('https://sandbox.duitku.com/webapi/api/merchant');
// $init->setPaymentURL('https://sandbox.duitku.com/webapi/api/merchant');
// $init->setCallbackURL('https://a.g-dev.io/secure/callback/demo');
// $init->setReturnURL('https://a.g-dev.io/secure/callback/demo');

// PROD
// $init = new \Growinc\Payment\Init('D6677', '9180265c1850e3ec2286f3b139d4c260');
$init = new \Growinc\Payment\Init('D5126', '85d4f1e881f4e602d5b8d52705746ee2'); // DuitkuGTI
$init->setBaseURI('https://passport.duitku.com/webapi/api/merchant');
$init->setPaymentURL('https://passport.duitku.com/webapi/api/merchant');
$init->setCallbackURL('https://a.g-dev.io/secure/callback/demo');
$init->setReturnURL('https://a.g-dev.io/secure/callback/demo');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081212121313');
$transaction->setAmount(100000);
// VC	Credit Card (Visa / Master)
// BK	BCA KlikPay
// M2	Mandiri Virtual Account
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
$transaction->setPaymentMethod('M2');

$vendor = new \Growinc\Payment\Vendors\Duitku($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	// $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
	extract($result);
	print_r($response);
	/*
	VA
	{
		"status": "000",
		"data": {
			"merchantCode": "D8014",
			"reference": "D8014J43L38A4WBVK36H",
			"paymentUrl": "https:\/\/sandbox.duitku.com\/topup\/topupdirectv2.aspx?ref=M1XHKFQMVCHERAK1Q",
			"vaNumber": "8903980801508272",
			"amount": "100000",
			"statusCode": "00",
			"statusMessage": "SUCCESS"
		}
	}
	Ritel (Alfamart dll)
	{
		"status": "000",
		"data": {
			"merchantCode": "D8014",
			"reference": "D8014KAK0QMTAAL81W3L",
			"paymentUrl": "https:\/\/sandbox.duitku.com\/topup\/topupdirectv2.aspx?ref=FTLI1JHIESTF00MXQ",
			"vaNumber": "021132640865",
			"amount": "100000",
			"statusCode": "00",
			"statusMessage": "SUCCESS"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()->getBody()->getContents()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
