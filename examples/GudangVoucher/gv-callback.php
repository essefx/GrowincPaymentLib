<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'878', // MerchantID
		'9485a72ed5fab4245f22ef97' // MerchantKey
	);

$vendor = new \Growinc\Payment\Vendors\GudangVoucher($init);

$callback_data = '{"data":"<trans_doc><merchant_id>878<\/merchant_id><merchant>VPAY<\/merchant><reference>8782021031818150440<\/reference><voucher_code>202103182554031616065898<\/voucher_code><amount currency=\"IDR\" nominal=\"14400\"\/><purpose>Tr Mr Dummy<\/purpose><custom>202103182554031616065898<\/custom><status>SUCCESS<\/status><\/trans_doc>"}';

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
