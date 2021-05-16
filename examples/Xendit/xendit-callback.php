<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		// Growinc
		'xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y', // secret_key
		'xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy' // public_key
	);
$vendor = new \Growinc\Payment\Vendors\Xendit($init);

try {
	$raw_data = file_get_contents("php://input");
	if (!$raw_data) {
		$raw_data = '{
			"amount": 50000,
			"callback_virtual_account_id": "5f8e658568ed5f402a1faadb",
			"payment_id": "5f8e658f8d65ab385241f17a",
			"external_id": "demo-va-callback",
			"account_number": "9999624535",
			"merchant_code": "88608",
			"bank_code": "MANDIRI",
			"transaction_timestamp": "2020-10-20T04:20:31.000Z",
			"currency": "IDR",
			"created": "2020-10-20T04:20:31.937Z",
			"updated": "2020-10-20T04:20:33.074Z",
			"id": "5f8e658fc5710a7ae00f75db",
			"owner_id": "5c2323c67d6d305ac433ba20"
		}';
	}
	$request = (object) json_decode($raw_data);
	$result = $vendor->Callback($request);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Callback failed: ' . $e->getMessage();
}
