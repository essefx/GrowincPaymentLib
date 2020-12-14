<?php 

// Api Key 1 = "9220fbdeb1d115a4f2e9b2636edc24cc";
// Api Key 2 = "5b74d200096570de0280b9838c7af1ab";
// Merchant Key = "c9c64d57f0c606ef06c297f96697cab4";

	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	require_once __DIR__ . '/../../vendor/autoload.php';

	/* init('apikey1', 'apikey2') */ 
	$init = new \Growinc\Payment\Init('9220fbdeb1d115a4f2e9b2636edc24cc','5b74d200096570de0280b9838c7af1ab'); 

	/* merchant key */ 
	$init->setMerchantKey('c9c64d57f0c606ef06c297f96697cab4');
	$init->setBaseURI('https://sandbox-payment.winpay.id');
	$init->setPaymentURL('https://sandbox-payment.winpay.id');

	$init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
	$init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

	$transaction = new \Growinc\Payment\Transaction();

	/*set cust information */ 
	$transaction->setCustomerName('LOREM');
	$transaction->setCustomerEmail('lorem@ipsum.com');
	$transaction->setCustomerPhone('081298983535');

	    /* start optional*/ 
	$transaction->setCountryCode('IDN');
	// $transaction->setAmount(100000);
	$transaction->setCustomerCity('Jakarta');
		/* end optional*/ 
	$transaction->setDescription('Product B00016 Baju Baru');
	$transaction->setCustomerAddress('Jl. Maju mundur kena');

	/* set Detail items */ 
	$item_detail = [
		["name" => "Baju Bali", "sku" => "01020304", "qty" => 2, "unitPrice" => 20000, "desc" => "Baju Tidur"],
		["name" => "Baju Bali", "sku" => "01020304", "qty" => 2, "unitPrice" => 12000, "desc" => "Baju Tidur"]
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