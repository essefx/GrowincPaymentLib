<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

/*
Merchant name: VoGame Indonesia
Merchant id: 33660
User id: bot33660
Password: p@ssw0rd
*/
$init = new \Growinc\Payment\Init(
		'VoGame Indonesia : 33660', // Name & MID
		'bot33660 : p@ssw0rd' // UserID & Pass
	);

$vendor = new \Growinc\Payment\Vendors\Faspay($init);

$callback_data = '
	{
		"request": "Payment Notification",
		"trx_id": "3366082500000174",
		"merchant_id": "33660",
		"merchant": "VoGame Indonesia",
		"bill_no": "1612782267",
		"payment_reff": "57910067322",
		"payment_date": "2021-02-08 18:11:35",
		"payment_status_code": "2",
		"payment_status_desc": "Payment Sukses",
		"bill_total": "6894",
		"payment_total": "6894",
		"payment_channel_uid": "825",
		"payment_channel": "CIMB VA",
		"signature": "ebebca6942f75a3c1fd72120c3ebbad384c973c0"
	}
';

try {
	$result = $vendor->Callback((object) json_decode($callback_data));
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"request": "Payment Notification",
			"trx_id": "3366082500000174",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"bill_no": "1612782267",
			"payment_reff": "57910067322",
			"payment_date": "2021-02-08 18:11:35",
			"payment_status_code": "2",
			"payment_status_desc": "Payment Sukses",
			"bill_total": "6894",
			"payment_total": "6894",
			"payment_channel_uid": "825",
			"payment_channel": "CIMB VA",
			"signature": "ebebca6942f75a3c1fd72120c3ebbad384c973c0"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
