<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		// Growinc
		'xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y', // secret_key
		'xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy' // public_key
	);
$init->setBaseURI('https://api.xendit.co/');
$init->setRequestURL('https://api.xendit.co/');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$result = $vendor->InquiryPayment((object) [
			'payment_id' => '5f9fb9758d65ab3c1141f230',
		]);
	// extract($result);
	// print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"payment_id": "5f9fb9758d65ab3c1141f230",
			"callback_virtual_account_id": "5f9fb82dbcbf722b71041f3f",
			"external_id": "VA_fixed-1604302892",
			"account_number": "9999000002",
			"bank_code": "MANDIRI",
			"amount": 50000,
			"transaction_timestamp": "2020-11-02T07:47:01.000Z",
			"merchant_code": "88608",
			"currency": "IDR",
			"id": "5f9fb9758940c131b3d7b96d"
		}
	}
	*/
	print_r($result);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
