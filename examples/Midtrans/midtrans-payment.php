<?php

// $server_key = "SB-Mid-server-4robMkuk3lusaK8mchsScfOM:";
// echo base64_encode($server_key);

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('SB-Mid-server-4robMkuk3lusaK8mchsScfOM','SB-Mid-client-bJ55mjtqpd-QbKWs'); /*(server_key , client_key)*/
$init->setBaseURI('https://api.sandbox.midtrans.com');
$init->setPaymentURL('https://api.sandbox.midtrans.com');
$init->setTokenURL('https://api.sandbox.midtrans.com/v2/token');
// $init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
// $init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081212121313');
// $transaction->setAmount(100000); // Inapplicable
$transaction->setCountrycode('IDN');

// midtrans should use item detail
$item_detail = [
	[
			"id" => "a1",
			"price" => 50000,
			"quantity" => 5,
			"name" => "apel",
			"brand" => "Fuji Apple",
			"category" => "Fruit",
			"merchant_name" => "Fruit-store"
		] // only cc
];
$transaction->setItem($item_detail);
$transaction->setDescription('Pembelian Elektronik');

// credit card
$transaction->setCardNumber('4811111111111114');
$transaction->setCardExpMonth('12');
$transaction->setCardExpYear('24');
$transaction->setCardExpCvv('123');

/*	paymentType AKA paymentMethod:
	bank transfer
		bank_transfer -> va
			permata, bni, bca, bri
		echannel (mandiri)
	internet banking (redirect url)
		bca_klikpay
		bca_klikbca (not activated)
		bri_epay (not activated)
		cimb_clicks
		danamon_online
	E-wallet
		qris (not activated)
		gopay
		shopeepay (not activated)
	telkomsel_cash (not activated)
	mandiri_ecash
	Over the Counter
		cstore
			indomaret
			alfamart
	akulaku
	credit_card
*/

/*------------------------------ V V V Start of Bank transfer ---------- */
// $transaction->setPaymentMethod('bank_transfer,permata');
// $transaction->setPaymentMethod('bank_transfer,bni');
$transaction->setPaymentMethod('bank_transfer,bca');
// $transaction->setPaymentMethod('bank_transfer,bri');
// $transaction->setPaymentMethod('echannel'); // Mandiri
/*------------------------------ A A A End of Bank transfer ---------- */

/*------------------------------ V V V Start of E-wallet ---------- */
// $transaction->setPaymentMethod('qris'); // Payment channel is not activated
// $transaction->setPaymentMethod('gopay');
// $transaction->setPaymentMethod('shopeepay'); // Payment channel is not activated
/*------------------------------ A A A End of E-wallet ---------- */

/*------------------------------ V V V Start of Convenience store ---------- */
// $transaction->setPaymentMethod('cstore,indomaret');
// $transaction->setPaymentMethod('cstore,alfamart');
/*------------------------------ A A A End of Convenience store ---------- */

// midtrans only for bca_klikbca
// $transaction->setCustomerUserID('midtrans1012');

$vendor = new \Growinc\Payment\Vendors\Midtrans($init);

try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	extract($result);
	print_r($response);
	//
	// Get  PSR7 object
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse());
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getCode();
}
