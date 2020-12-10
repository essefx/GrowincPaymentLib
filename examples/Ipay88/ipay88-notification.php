<?php 
  //  error_reporting(E_ALL);
	//  ini_set('display_errors', 1);

  require_once __DIR__ . '/../../vendor/autoload.php';

	$init = new \Growinc\Payment\Init('ID01625', '1gUbnGkdKA');
	$vendor = new \Growinc\Payment\Vendors\Ipay88($init);
	
	try {
		$raw_data = file_get_contents("php://input");
		if (!$raw_data) {
			/** 
			 * AuthCode : Kode Persetujuan Bank
			 * Status : 	 
			 * 	 0 => Fail
			 * 	 6 => Pending
			*/
			$raw_data = '{
				"MerchantCode"	: "ID01625",
				"PaymentId"		: "9",
				"RefNo"		: "123039600",
				"Amount"		: "250000",
				"Currency"		: "IDR",
				"AuthCode"		: "0931231",    
				"Status"		: "6",
				"Signature"		: "h2vigzCpudLWMwCb2fryh2MUXTA=",
				"CheckoutURL"	: "https://sandbox.ipay88.co.id/epayment/entry.asp?CheckoutID=5F822C024A102470C16A762C19EA29D7879A47B2EFF7C4151E309F00EDEADC6F&Signature=Nv2ub5JULwXf1X2x7B9CLe3z7K4%3d"
			}';
		}

		$request = (object) json_decode($raw_data);

		// $request = 'h2vigzCpudLWMwCb2fryh2MUXTA=';

		$result = $vendor->Notification($request);
		extract($result);
		print_r($response);
	} catch (\Throwable $e) {
		echo 'Callback failed: ' . $e->getMessage() . ':' . $e->getCode();
	}

	