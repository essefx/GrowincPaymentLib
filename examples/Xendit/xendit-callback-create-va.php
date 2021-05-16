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
			"id": "57fb4e076fa3fa296b7f5a97",
			"owner_id": "5824128aa6f9f9b648be9d76",
			"external_id": "va-1487156410",
			"merchant_code": "88608",
			"account_number": "886081000123456",
			"bank_code": "MANDIRI",
			"name": "John Doe",
			"is_closed": false,
			"is_single_use": false,
			"status": "ACTIVE",
			"expiration_date": "2048-02-15T11:01:52.722Z",
			"updated": "2016-10-10T08:15:03.404Z",
			"created": "2016-10-10T08:15:03.404Z"
		}';
	}
	$request = (object) json_decode($raw_data);
	$result = $vendor->CallbackAlt($request);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Callback failed: ' . $e->getMessage();
}
