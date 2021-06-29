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
$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
	$raw_data = file_get_contents("php://input");
	// Input dummy sample
	// $raw_data = '{
	// 	"rq_uuid": "e9b185e4-031d-4794-bc21-1a3282506051",
	// 	"rq_datetime": "2021-06-18 14:14:39",
	// 	"sender_id": "SGOPLUS",
	// 	"receiver_id": "SGWGROWINC",
	// 	"password": "Y0F,(5EM=#",
	// 	"comm_code": "SGWGROWINC",
	// 	"member_code": "",
	// 	"order_id": "0024000477",
	// 	"signature": "2163c0d2b0148d37b8c05630fe8bb39e5fbcbb6ad4303c1c25c66ff7db3337bb"
	// }';

	$file = '../_log/espay_log_inquiry_' . time();
	// file_put_contents($file . '_raw.txt', $raw_data, FILE_APPEND | LOCK_EX);

	if (!$raw_data) {
		print_r('{data:null}');
		// throw new \Exception('Inquiry request is empty');
	} else {
		$raw_data = parse_str($raw_data, $data);
		$request = (object) $data;
		//
		file_put_contents($file . '.txt',
			json_encode($request, JSON_PRETTY_PRINT),
			FILE_APPEND | LOCK_EX);
		//
		$result = $vendor->IncomingInquiry($request);
		print_r($result);
	}
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
