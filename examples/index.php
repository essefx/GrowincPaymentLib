<?php

require_once __DIR__ . '/../vendor/autoload.php';

$setup = new \Growinc\Payment\Setup('D6677', '9180265c1850e3ec2286f3b139d4c260');
$setup->setURL([
		'payment_url' => 'https://sandbox.duitku.com/webapi/api/merchant',
		// 'payment_url' => 'https://google.com',
		'callback_url' => 'https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result',
		'return_url' => 'https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form',
	]);

$duitku = new \Growinc\Payment\Vendors\Duitku($setup);
$duitku->Index();
// $duitku->RedirectPayment([

// 	]);

// print_r($setup);
// print_r($duitku);

