<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

// Growinc
$merchant_code = 'SGWGROWINC';
$api_key = '66a82db380f34bdfa9b1738eacfb1ac6';
$signature = 'ces0bu1jh9qrsakq';
$password = 'Y0F,(5EM=#';
// Selaras
// $merchant_code = 'SGWVOGAME';
// $api_key = 'def4e8b9a7c05937db137488858a5b45';
// $signature = 'w76g0p75rz07wek7';
// $password = '6T,Y@0O3^P';

$init = new \Growinc\Payment\Init($merchant_code, $api_key);
$init->setPaymentURL('https://sandbox-api.espay.id/');
$init->setParams([
	'signature' => $signature,
	'password' => $password,
]);

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
//
$transaction->setItem('Voucher 01');
// $transaction->setAmount(rand(5000,10000) * 100);
$transaction->setAmount(100000);
$transaction->setDescription('Product Voucher Baru');
//
// $transaction->setPaymentMethod('va,008');
//
$transaction->setPaymentMethod('ibank,014'); // BCA VA Online
// $transaction->setPaymentMethod('ibank,022'); // VA CIMB Niaga
// $transaction->setPaymentMethod('ibank,011'); // Danamon Online Banking
// $transaction->setPaymentMethod('ibank,046'); // DBS VA
// $transaction->setPaymentMethod('ibank,008'); // Mandiri VA
// $transaction->setPaymentMethod('ibank,016'); // Maybank VA
// $transaction->setPaymentMethod('ibank,013'); // Permata VA
//
// $transaction->setPaymentMethod('ewallet,ovo');
// $transaction->setPaymentMethod('ewallet,jenius');
// $transaction->setPaymentMethod('ewallet,gopay');
// $transaction->setPaymentMethod('ewallet,linkaja');
//
// $transaction->setPaymentMethod('cstore,066'); // indomaret
// $transaction->setPaymentMethod('cstore,026'); // alfamart

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($response);
	/* // Success
	{
		"status": "000",
		"data": {
			"rq_uuid": "INV24011177",
			"rs_datetime": "2021-06-18 17:13:05",
			"error_code": "0000",
			"error_message": "",
			"va_number": "8920800481110560",
			"expired": "2021-06-18 19:13:04",
			"description": "Payment",
			"total_amount": "100000.00",
			"amount": "100000.00",
			"fee": "0.00",
			"bank_code": "008"
		}
	}
	 */
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
