<?php

require_once __DIR__ . '/../vendor/autoload.php';

$init = new \Growinc\Payment\Init('D6677', '9180265c1850e3ec2286f3b139d4c260');
$init->setBaseURI('https://sandbox.duitku.com/webapi/api/merchant');
$init->setPaymentURL('https://sandbox.duitku.com/webapi/api/merchant');

$vendor = new \Growinc\Payment\Vendors\Duitku($init);

try {
	$raw_data = file_get_contents("php://input");
	if (!$raw_data) {
		$raw_data = '{
				"merchantCode": "D6677",
				"amount": "100000",
				"merchantOrderId": "0001285662",
				"productDetail": "Payment for order 0001285662",
				"additionalParam": null,
				"resultCode": "00",
				"signature": "439030a6da086ee13558137f07d4a27d",
				"paymentCode": "VC",
				"merchantUserId": null,
				"reference": "D6677JXVYL752HMAV0AD"
			}';
	}
	$request = (object) json_decode($raw_data);
	$result = $vendor->Callback($request);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Failed to process callback: ' . $e->getMessage();
}
