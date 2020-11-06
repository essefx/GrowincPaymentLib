<?php

require_once __DIR__ . '/../vendor/autoload.php';

// $init = new \Growinc\Payment\Init('360360000000125', '9E3D31AAA24699C482887E0E47A35DF13F422C40EBF860008B8B8C45347E1C69');
// $init = new \Growinc\Payment\Init('360360000000200', '4AC61F32A209A56B95712E0394E44AE620DD37ACD27C41AB64F4A99B22751420');
$init = new \Growinc\Payment\Init('JT03', 'uA7yXRa8PjVe');
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

$vendor = new \Growinc\Payment\Vendors\TCTP($init);

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('SEAN');
$transaction->setCustomerEmail('essefx@gmail.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setAmount(100000);
$transaction->setCurrencyCode('IDR');
$transaction->setPaymentMethod('CC');
$transaction->setDescription('PAYMENT');
$transaction->setInvoiceNo('10001');

try {
	$result = $vendor->SecurePayment($transaction);
	extract($result);
	print_r($result);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}
