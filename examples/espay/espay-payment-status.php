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
$init->setRequestURL('https://sandbox-api.espay.id/');
$init->setParams([
	'signature' => $signature,
	'password' => $password,
]);

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => $_REQUEST['order_id'] ?? '0008032194',
		]);
	// extract($result);
	// print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"rq_uuid": "1624632024",
			"rs_datetime": "2021-06-25 21:40:26",
			"error_code": "0000",
			"error_message": "",
			"comm_code": "SGWGROWINC",
			"member_code": null,
			"tx_id": "ESP1608032198KBSU",
			"order_id": "0008032194",
			"ccy_id": "IDR",
			"amount": "180000",
			"refund_amount": 0,
			"tx_status": "IP",
			"tx_reason": "EXPIRED",
			"tx_date": "2020-12-15",
			"created": "2020-12-15 18:36:35",
			"expired": "2020-12-16 18:36:38",
			"bank_name": "BANK MANDIRI",
			"product_name": "Link Aja QR Pay",
			"product_value": "",
			"payment_ref": "",
			"merchant_code": "1002",
			"token": "",
			"member_cust_id": "SYSTEM",
			"member_cust_name": "SYSTEM",
			"debit_from_name": "",
			"debit_from_bank": "008",
			"credit_to": "1150000059313",
			"credit_to_name": "1150000059313",
			"credit_to_bank": "008",
			"payment_datetime": "2020-12-15 18:36:38"
		}
	}
	*/
	print_r($result);
} catch (\Throwable $e) {
	echo 'Payment status failed: ' . $e->getMessage();
}
