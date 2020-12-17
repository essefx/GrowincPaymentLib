<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y', // secret_key
		'xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy' // public_key
	);
$init->setBaseURI('https://api.xendit.co/');
$init->setRequestURL('https://api.xendit.co/');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$result = $vendor->Inquiry((object) [
			'id' => '5fd9e479ed81dd402014403c',
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
