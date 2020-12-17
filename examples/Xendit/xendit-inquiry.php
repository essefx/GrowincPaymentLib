<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'xnd_development_hhT1ZEIAtpjt7JpDNovnNdbDJbTAaVn7XSpm6ZLDklJz4xEkrj4pjWWtQB1LDZV', // secret_key
		'xnd_public_development_0Bwtm2oo6DmSuPSTBdEYN55hJOhlCrMdRwTQjq8OTssPVY8cKG2TZg5wCeIJxek' // public_key
	);
$init->setBaseURI('https://api.xendit.co/');
$init->setRequestURL('https://api.xendit.co/');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$result = $vendor->Inquiry((object) [
			'id' => '5fd9d1cded81dd402014400c',
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
