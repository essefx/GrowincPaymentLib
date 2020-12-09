<?php 

// Api Key 1 = "9220fbdeb1d115a4f2e9b2636edc24cc";
// Api Key 2 = "5b74d200096570de0280b9838c7af1ab";
// Merchant Key = "c9c64d57f0c606ef06c297f96697cab4";

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('9220fbdeb1d115a4f2e9b2636edc24cc','5b74d200096570de0280b9838c7af1ab'); /*(server_key , client_key)*/
$init->setMerchantKey('c9c64d57f0c606ef06c297f96697cab4');
$init->setBaseURI('https://sandbox-payment.winpay.id');
$init->setPaymentURL('https://sandbox-payment.winpay.id');

// redirect ke halaman kita 
$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
// notifikasi 
$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');


$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081298983535');

$item_detail = [
  ["name" => "Baju Bali", "sku" => "01020304", "qty" => 2, "unitPrice" => 20000, "desc" => "Baju Tidur"],
  ["name" => "Baju Bali", "sku" => "01020304", "qty" => 2, "unitPrice" => 12000, "desc" => "Baju Tidur"]
];

$transaction->setItem($item_detail);

/*	paymentType:
BCAVA, BRIVA, BNIVA, MANDIRIVA, atau PERMATAVA
*/
$transaction->setPaymentMethod('MANDIRIVA'); 



$vendor = new \Growinc\Payment\Vendors\Winpay($init);
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