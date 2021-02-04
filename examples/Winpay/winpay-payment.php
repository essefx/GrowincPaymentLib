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
	$transaction->setDescription('Product Game Baru');
	$transaction->setCustomerAddress('Jl. Maju mundur kena');

	/* set Detail items */ 
	$item_detail = [
		["name" => "game 01", "sku" => "02020225", "qty" => 2, "unitPrice" => 20000, "desc" => "game 01"],
		["name" => "game 02", "sku" => "02020225", "qty" => 2, "unitPrice" => 12000, "desc" => "game 02"]
	];
	$transaction->setItem($item_detail);

 	/*	paymentType:

		--> bank_transfer
		BCA VA			=> bank_transfer,bca
		BNI VA			=> bank_transfer,bni
		BRI					=> bank_transfer,bri
		MANDIRI VA	=> bank_transfer,mandiri
		PERMATA VA	=> bank_transfer,permata

	  	--> cstore
	    INDOMARET		=> cstore,indomaret
	    ALFAMART		=> cstore,alfamart
		FASTPAY			=> cstore,fastpay 

		--> pulsa
	    Indosat Dompetku	=> pulsa,indosat ( redirect )
	    Telkomsel Cash		=> pulsa,telkomsel
		XL Tunai			=> pulsa,xl

		--> payment_code
	    ATM 137 Bank		=> payment_code,ATM137
	    ATM BCA				=> payment_code,BCAPC
		BebasBayar			=> payment_code,BEBASBAYAR ( redirect )
		BCA Klik Pay		=> payment_code,BCAKP ( redirect )
		CIMB Clicks			=> payment_code,CIMBC ( redirect )
		Danamon Online Banking	=> payment_code,DANAMON ( redirect )
		Debit Online BTN	=> payment_code,BTNONLINE ( redirect )
		E-Pay BRI			=> payment_code,BRIEP ( redirect )
		FinPay Code			=> payment_code,FINPAY 
		Kartu Kredit		=> payment_code,KKWP ( redirect )
		Mandiri ECash		=> payment_code,MANDIRIEC ( redirect )
		Mandiri Pay Code	=> payment_code,MANDIRIPC
		Mandiri Click		=> payment_code,MANDIRICP ( redirect )
		IB Muamalat			=> payment_code,MUAMALAT ( redirect )
	
	*/


	// 00	Success
	// 01	Access Denied! not authorized
	// 04	Data not found
	// 05	General Error
	// 99	Parameter not valid

	/*** set payment method ***/ 
	// $transaction->setPaymentMethod('bank_transfer,mandiri');
	/*** e-wallet ***/
	$transaction->setPaymentMethod('qris,qris');
	/*** pulsa ***/
	// $transaction->setPaymentMethod('pulsa,telkomsel');
	/*** payment_code ***/
	// $transaction->setPaymentMethod('payment_code,finpay');
	
	
	/* call vendor */ 
	$vendor = new \Growinc\Payment\Vendors\Winpay($init);

	// $result = $vendor->SecurePayment($transaction);
	// print_r($result);exit();
	
	try {
		$result = $vendor->SecurePayment($transaction); // return payment URL
		
		// $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
		extract($result);
		print_r($result);
		//
		// print_r($vendor->getRequest());
		// print_r($vendor->getResponse()); // Get  PSR7 object
	} catch (\Throwable $e) {
		echo 'Payment failed: ' . $e->getMessage();
	}
  

?>