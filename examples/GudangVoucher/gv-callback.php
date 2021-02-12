<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'878', // MerchantID
		'9485a72ed5fab4245f22ef97' // MerchantKey
	);

$vendor = new \Growinc\Payment\Vendors\GudangVoucher($init);

$callback_data = '{"data":"<trans_doc><merchant_id>70</merchant_id><merchant>Demo BMT</merchant><reference>GV35519829368965</reference><amount currency="IDR" nominal="150000"/><purpose>10 FaceBook Credit </purpose><custom>FBorder23May12-123456</custom><status>FAIL</status>"}';

try {
	$result = $vendor->Callback((object) json_decode($callback_data));
	extract($result);
	print_r($response);
	// Success
	/*
	{
		"status": "000",
		"data": {
			"merchant_id": "878",
			"merchant": "VPAY",
			"reference": "87820210208154546",
			"voucher_code": "0012773633",
			"amount": {
				"@attributes": {
					"currency": "IDR",
					"nominal": "560000"
				}
			},
			"purpose": "Apple",
			"custom": "0012773633",
			"status": "SUCCESS",
			"development": "YES"
		}
	}
	*/
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
