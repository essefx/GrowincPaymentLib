<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('ID01676', '0XScE2NdvU');
$init->setBaseURI('https://sandbox.ipay88.co.id');
$init->setRequestURL('https://payment.ipay88.co.id/epayment/enquiry.asp');

$vendor = new \Growinc\Payment\Vendors\Ipay88($init);

try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '0013014705',
			'amount' => '10000',
		]);
	// extract($result);
	// print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"is_closed": true,
			"status": "ACTIVE",
			"currency": "IDR",
			"owner_id": "5f706881fefc961e3f708f02",
			"external_id": "0008115320",
			"bank_code": "BCA",
			"merchant_code": "10766",
			"name": "LOREM IPSUM",
			"account_number": "107669999020779",
			"expected_amount": 100000,
			"expiration_date": "2020-12-16T12:22:00.000Z",
			"is_single_use": false,
			"id": "5fd9e479ed81dd402014403c"
		}
	}
	*/
	print_r($result);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
