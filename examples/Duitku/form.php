<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('360360000000125', '9E3D31AAA24699C482887E0E47A35DF13F422C40EBF860008B8B8C45347E1C69');
$init->setBaseURI('https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment');
$init->setPaymentURL('https://demo2.2c2p.com/2C2PFrontEnd/RedirectV3/payment');
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('SEAN');
$transaction->setCustomerEmail('essefx@gmail.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setAmount(100000);
$transaction->setPaymentMethod('BANK');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

try {
	$result = $vendor->RedirectPayment($transaction);
	extract($result);
	print_r($result);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
