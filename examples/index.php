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

$request = new \Growinc\Payment\Vendors\Duitku($init, $transaction);
$result = $request->RedirectPayment();

// print_r($result);

// $setup->setURL([
// 		'payment_url' => 'https://sandbox.duitku.com/webapi/api/merchant',
// 		// 'payment_url' => 'https://google.com',
// 		'callback_url' => 'https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result',
// 		'return_url' => 'https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form',
// 	]);

// $duitku = new \Growinc\Payment\Vendors\Duitku($setup);
// $duitku->Index();
// $duitku->RedirectPayment([
// 		'amount' => 10000,
// 		'customer_name' => 'SEAN',
// 		'customer_email' => 'essefx@gmail.com',
// 		'customer_phone' => '081298983535',
// 	]);

// print_r($setup);
// print_r($duitku);

/*
	$time = time();
	$payment = (object) [];
	$payment->payment_url = 'https://pga.growinc.dev/webapi/pay/create';
	$payment->merchant_code = 'PGA20YQMX';
	$payment->secret = 'v8WkpFB$TUBXGS4Kn3LkPt2L6';
	$payment->redirect_url = 'http://localhost/git/GrowincPaymentLib/examples/callback.php';
	$payment->invoice_no = 'INV' . substr($time, 2, strlen($time));
	$payment->description = 'Payment for order ' . $payment->invoice_no;
	$payment->customer_name = 'SEAN';
	$payment->customer_email = 'essefx@gmail.com';
	$payment->customer_phone = '081298983535';
	$payment->expire_id = '100';
	$payment->pattern = $payment->merchant_code . ':' . $payment->invoice_no;
	$payment->signature = hash_hmac('sha256', $payment->pattern, $payment->secret, false);	//Compute hash value

	echo '
<!DOCTYPE HTML>
<html>
<head>
	<title>Redirect Payment #' . $payment->invoice_no . '</title>
	<script src="https://code.jquery.com/jquery-1.11.3.js" type="text/javascript"></script>
</head>
<body>
	<form action="' . $payment->payment_url . '" method="post" name="payment-form" id="payment-form" style="display:inline;">

		<input type="hidden" name="merchant_code" value="' . $payment->merchant_code . '" />
		<input type="hidden" name="redirect_url" value="' . $payment->redirect_url . '" />
		<input type="hidden" name="expire_id" value="' . $payment->expire_id . '" />
		<input type="hidden" name="signature" value="' . $payment->signature . '" />

		invoice_no <input type="text" name="invoice_no" value="' . $payment->invoice_no . '" /><br/>
		description <input type="text" name="description" value="' . $payment->description . '" /><br/>
		customer_name <input type="text" name="customer_name" value="' . $payment->customer_name . '" /><br/>
		customer_phone <input type="text" name="customer_phone" value="' . $payment->customer_phone . '" /><br/>
		customer_email <input type="text" name="customer_email" value="' . $payment->customer_email . '" /><br/>

		<input type="submit" name="submit" id="submit" value="Submit" />
	</form>
</body>
</html>
';
*/