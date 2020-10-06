<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = new \Growinc\Payment\Client('D6677', '9180265c1850e3ec2286f3b139d4c260');
$client->setURL(
		'https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form',
		'https://sandbox.duitku.com/webapi/api/merchant',
		'https://sandbox.duitku.com/webapi/api/merchant'
	);

$transaction = new \Growinc\Payment\Vendors\Duitku($client);
$transaction->Index();

// print_r($client);

