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
		// 'VoGame Indonesia : 33660', // Name & MID
		// 'bot33660 : p@ssw0rd' // UserID & Pass
		'PT Growinc Teknologi Indonesia : 33495', // Name & MID
		'bot33495 : p@ssw0rd' // UserID & Pass
	);
// Dev URL
$init->setRequestURL('https://dev.faspay.co.id/'); // Payment Inquiry
// LIVE URL
// $init->setRequestURL('https://web.faspay.co.id/'); // Payment Inquiry

$vendor = new \Growinc\Payment\Vendors\Faspay($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '1615965416',
			'trx_id' => '3349540200000042', // Faspay transaction ID
		]);
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"response": "Inquiry Status Payment",
			"trx_id": "3366080100000042",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"bill_no": "1612772364",
			"payment_reff": "",
			"payment_date": "",
			"payment_status_desc": "Belum diproses",
			"payment_status_code": "0",
			"payment_total": "",
			"response_code": "00",
			"response_desc": "Sukses"
		}
	}
	{
		"status": "000",
		"data": {
			"response": "Inquiry Status Payment",
			"trx_id": "3366082500000166",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"bill_no": "1612772509",
			"payment_reff": "",
			"payment_date": "",
			"payment_status_desc": "Belum diproses",
			"payment_status_code": "0",
			"payment_total": "",
			"response_code": "00",
			"response_desc": "Sukses"
		}
	}
	*/
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
