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
$init->setParams([
	'signature' => $signature,
	'password' => $password,
]);

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
	$raw_data = file_get_contents("php://input");
	// Input dummy sample
	// $raw_data = '
	// 	{
	// 		"rq_uuid": "403e7d8b-5f57-417d-82ae-67ebc02bbbdd",
	// 		"rq_datetime": "2021-06-25 15:15:15",
	// 		"sender_id": "SGOPLUS",
	// 		"receiver_id": "SGWGROWINC",
	// 		"password": "Y0F,(5EM=#",
	// 		"comm_code": "SGWGROWINC",
	// 		"member_code": "4490587880694973",
	// 		"member_cust_id": "SYSTEM",
	// 		"member_cust_name": "SYSTEM",
	// 		"ccy": "IDR",
	// 		"amount": "100000",
	// 		"debit_from": "4490587880694973",
	// 		"debit_from_name": "4490587880694973",
	// 		"debit_from_bank": "014",
	// 		"credit_to": "1111111111111",
	// 		"credit_to_name": "ESPAY AGGREGATOR",
	// 		"credit_to_bank": "014",
	// 		"payment_datetime": "2021-06-25 15:15:14",
	// 		"payment_ref": "ESP1624608878QS4U",
	// 		"payment_remark": "Payment",
	// 		"order_id": "0024608873",
	// 		"product_code": "BCAATM",
	// 		"product_value": "4490587880694973",
	// 		"message": "{\"CHANNEL_FLAG\":\"A\",\"PAY_AMOUNT_VA\":\"100000\"}",
	// 		"status": "0",
	// 		"token": "",
	// 		"total_amount": "100000.00",
	// 		"tx_key": "ESP1624608878QS4U",
	// 		"fee_type": "S",
	// 		"tx_fee": "0.00",
	// 		"approval_code": "16246089143418135895",
	// 		"member_id": "4490587880694973",
	// 		"approval_code_full_bca": "4490587880694973",
	// 		"signature": "faaf0dc3c372f9eff435d36b9cba0c6ce87891e90bb4aa0fb0b656b787f45fc5"
	// 	}
	// 	';

	$file = '../_log/espay_log_notif_' . time();
	// file_put_contents($file . '_raw.txt', $raw_data, FILE_APPEND | LOCK_EX);

	if (!$raw_data) {
		print_r('{data:null}');
		// throw new \Exception('Notification request is empty');
	} else {
		$raw_data = parse_str($raw_data, $data);
		$request = (object) $data;
		//
		file_put_contents($file . '.txt',
			json_encode($request, JSON_PRETTY_PRINT),
			FILE_APPEND | LOCK_EX);
		//
		$result = $vendor->IncomingNotification($request);
		print_r($result);
	}
} catch (\Throwable $e) {
	echo 'Notification failed: ' . $e->getMessage();
}
