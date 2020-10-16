<?php

	$time = time();
	$payment = (object) [];
	$payment->payment_url = 'https://pga.growinc.dev/webapi/pay/create';
	$payment->merchant_code = 'PGA20YQMX';
	$payment->secret = 'v8WkpFB$TUBXGS4Kn3LkPt2L6';
	$payment->redirect_url = 'http://localhost/callback.php';
	$payment->invoice_no = 'INV' . substr($time, 2, strlen($time));
	$payment->description = 'Payment for order ' . $payment->invoice_no;
	$payment->customer_name = 'SEAN';
	$payment->customer_email = 'essefx@gmail.com';
	$payment->customer_phone = '081298983535';
	$payment->expire_id = '100';
	$payment->pattern = $payment->merchant_code . ':' . $payment->invoice_no;
	$payment->signature = hash_hmac('sha256', $payment->pattern, $payment->secret, false);

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

?>