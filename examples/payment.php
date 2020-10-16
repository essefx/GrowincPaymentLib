<?php

require_once __DIR__ . '/../vendor/autoload.php';

$init = new \Growinc\Payment\Init('D6677', '9180265c1850e3ec2286f3b139d4c260');
$init->setBaseURI('https://sandbox.duitku.com/webapi/api/merchant');
$init->setPaymentURL('https://sandbox.duitku.com/webapi/api/merchant');
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('SEAN');
$transaction->setCustomerEmail('essefx@gmail.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setAmount(100000);
$transaction->setPaymentMethod('VC');

$vendor = new \Growinc\Payment\Vendors\Duitku($init);

try {
	$result = $vendor->RedirectPayment($transaction);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Failed to transaction: ' . $e->getCode();
}
