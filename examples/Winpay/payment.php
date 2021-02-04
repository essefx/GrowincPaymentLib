<?php 

// Api Key 1 = "9220fbdeb1d115a4f2e9b2636edc24cc";
// Api Key 2 = "5b74d200096570de0280b9838c7af1ab";
// Merchant Key = "c9c64d57f0c606ef06c297f96697cab4";

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once __DIR__ . '/../../vendor/autoload.php';

	/* init('apikey1', 'apikey2') */ 
	$init = new \Growinc\Payment\Init('000f1f4cb5118390cc2ec79af671d617','19c6f7a74281b16c2e70ba485dcf1750');

	/* merchant key */ 
	$init->setMerchantKey('a85b54a715b31119a654928c400c8bb8');
	$init->setBaseURI('https://secure-payment.winpay.id');
	$init->setPaymentURL('https://secure-payment.winpay.id');

	$init->setCallbackURL('https://vogame.dev/callbackUrl');
	$init->setReturnURL('https://vogame.dev/returnUrl');

	$transaction = new \Growinc\Payment\Transaction();

	/*set cust information */ 
	$transaction->setCustomerName('LOREM');
	$transaction->setCustomerEmail('lorem@ipsum.com');
	$transaction->setCustomerPhone('081212121234');

	    /* start optional*/ 
	$transaction->setCountryCode('IDN');
	// $transaction->setAmount(100000);
	$transaction->setCustomerCity('Jakarta');
		/* end optional*/ 
	$transaction->setDescription('Product Game');
	$transaction->setCustomerAddress('Jl. Maju mundur kena');

	/* set Detail items */ 
	$item_detail = [
		["name" => "Game 01", "sku" => "01020304", "qty" => 2, "unitPrice" => 20000, "desc" => "Game 01"],
		["name" => "Game 01", "sku" => "01020304", "qty" => 2, "unitPrice" => 12000, "desc" => "Game 01"]
	];
	$transaction->setItem($item_detail);

 /*	paymentType:

	--> bank_transfer

		BCA VA			=> bank_transfer,bcava
		BNI VA			=> bank_transfer,bniva
		BRI					=> bank_transfer,briva
		MANDIRI VA	=> bank_transfer,mandiriva
		PERMATA VA	=> bank_transfer,permatava

  --> payment_code

    INDOMARET		=> payment_code,indomaret
    ALFAMART		=> payment_code,alfamart
		FASTPAY			=> payment_code,fastpay
	
	*/


	// 00	Success
	// 01	Access Denied! not authorized
	// 04	Data not found
	// 05	General Error
	// 99	Parameter not valid

	/* set payment method */ 
	$transaction->setPaymentMethod('bank_transfer,mandiri');

	/* call vendor */ 
	$vendor = new \Growinc\Payment\Vendors\Winpay($init);

	$result = $vendor->SecurePayment($transaction);

	
	try {
		$result = $vendor->SecurePayment($transaction); // return payment URL
		print_r($result);exit();
		// $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
		// extract($result);

		// print($response);
		// print_r($response);
		//
		// print_r($vendor->getRequest());
		// print_r($vendor->getResponse()); // Get  PSR7 object
	} catch (\Throwable $e) {
		echo 'Payment failed: ' . $e->getCode();
	}
  

?>