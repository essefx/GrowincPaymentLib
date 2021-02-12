<?php

date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
		'878', // MerchantID
		'9485a72ed5fab4245f22ef97' // MerchantKey
	);
$init->setRequestURL('https://www.gudangvoucher.com/cpayment.php');

$vendor = new \Growinc\Payment\Vendors\GudangVoucher($init);


try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '0012773633',
		]);
	extract($result);
	print_r($response);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getMessage();
}
