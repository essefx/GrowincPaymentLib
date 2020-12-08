<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('SB-Mid-server-4robMkuk3lusaK8mchsScfOM');
$init->setBaseURI('https://api.sandbox.midtrans.com');
$init->setRequestURL('https://api.sandbox.midtrans.com/v2/');

$vendor = new \Growinc\Payment\Vendors\Midtrans($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '0007398788',
		]);
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"va_numbers": [{
				"bank": "bca",
				"va_number": "53042024029"
			}],
			"payment_amounts": [],
			"transaction_time": "2020-12-08 10:39:48",
			"gross_amount": "250000.00",
			"currency": "IDR",
			"order_id": "0007398788",
			"payment_type": "bank_transfer",
			"signature_key": "4be7d4d95a8359103e916105947ef68837c3e705118569317d4a462d128cb667c554526c4759fc02a245d17ea82469f5716aa2782ba4ea2086f1dd8fb873b8d1",
			"status_code": "201",
			"transaction_id": "7a1b5e78-4238-4849-8322-af160f652f33",
			"transaction_status": "pending",
			"fraud_status": "accept",
			"status_message": "Success, transaction is found",
			"merchant_id": "G345053042"
		}
	}
	*/
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
