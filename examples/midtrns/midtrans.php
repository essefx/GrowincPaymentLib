<?php 

// $server_key = "SB-Mid-server-4robMkuk3lusaK8mchsScfOM:";
// echo base64_encode($server_key);


require_once __DIR__ . '/../vendor/autoload.php';

$init = new \Growinc\Payment\Init('SB-Mid-server-4robMkuk3lusaK8mchsScfOM','SB-Mid-client-bJ55mjtqpd-QbKWs'); /*(server_key , client_key)*/
$init->setBaseURI('https://api.sandbox.midtrans.com');
$init->setPaymentURL('https://api.sandbox.midtrans.com');
$init->setTokenUrl('https://api.sandbox.midtrans.com/v2/token');
// $init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
// $init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setAmount(100000);
$transaction->setCountrycode('IDN');

// credit card
$transaction->setCardNumber('4811111111111114');
$transaction->setCardExpMonth('12');
$transaction->setCardExpYear('24');
$transaction->setCardExpCvv('123');

/*	paymentType:
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
// midtrans param required
$transaction->setPaymentMethod('bca'); // midtrans: only for VA & cstore (indomaret & alfamart)
$transaction->setPaymentType('credit_card');
$transaction->setDescription('Pembelian Elektronik');

// midtrans only for bca_klikbca
$transaction->setCustomerUserid('midtrans1012');

// midtrans should use item detail
$item_detail = [
	// ["id" => "item01", "price" => 50000, "quantity" => 2, "name" => "Ayam Zozozo"],
	// ["id" => "item01", "price" => 30000, "quantity" => 5, "name" => "Ayam Zozozo"],
	["id" => "a1", "price" => 50000, "quantity" => 5, "name" => "apel", "brand" => "Fuji Apple", "category" => "Fruit","merchant_name" => "Fruit-store"] //only cc
];
$transaction->setItem($item_detail);

$vendor = new \Growinc\Payment\Vendors\Midtrans($init);
// $result = $vendor->SecurePayment($transaction);
// print_r($result);exit();
 
try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	// $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
	extract($result);
	print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getCode();
}
  

?>    