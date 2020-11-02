<?php

// require_once __DIR__ . '/../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('NjI4MTI5ODk4MzUzNTpLb2dpNDkkYjNKYVk=');
$vendor = new \Growinc\Payment\Vendors\Espay($init);
// print_r($vendor);
// return;
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
    echo 'Callback failed: ' . $e->getMessage() . ':' . $e->getCode();
}