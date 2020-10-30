<?php

require_once __DIR__ . '/../vendor/autoload.php';

$init = new \Growinc\Payment\Init('SB-Mid-server-4robMkuk3lusaK8mchsScfOM');
$vendor = new \Growinc\Payment\Vendors\Midtrans($init);

// $raw_data = file_get_contents("php://input");
	// if (!$raw_data) {
		// $raw_data = '{
			  // "status_code": "201",
			  // "status_message": "midtrans payment notification",
			  // "transaction_id": "6fd88567-62da-43ff-8fe6-5717e430ffc7",
			  // "order_id": "0003960969",
			  // "gross_amount": "150000.00",
			  // "payment_type": "bank_transfer",
			  // "transaction_time": "2016-06-19 18:23:21",
			  // "transaction_status": "settlement",
			  // "fraud_status": "accept",
			  // "permata_va_number": "8562000087926752",
			  // "signature_key": "b8d7baceab8967af2fdebb82f497fbf4be957e0147f34e910fe9abfc533f883f1206e6c7a72d111ff61331254e3ff9f609c16cc81762e15d9ee6c53de36c65ff"
			// }';
	// }
	// $request = (object) json_decode($raw_data);
	// $result = $vendor->Callback($request);
	// extract($result);
	// print_r($response);
// exit();

try {
	$raw_data = file_get_contents("php://input");
	if (!$raw_data) {
		$raw_data = '{
			  "status_code": "201",
			  "status_message": "midtrans payment notification",
			  "transaction_id": "6fd88567-62da-43ff-8fe6-5717e430ffc7",
			  "order_id": "0003960969",
			  "gross_amount": "150000.00",
			  "payment_type": "bank_transfer",
			  "transaction_time": "2016-06-19 18:23:21",
			  "transaction_status": "settlement",
			  "fraud_status": "accept",
			  "permata_va_number": "8562000087926752",
			  "signature_key": "b8d7baceab8967af2fdebb82f497fbf4be957e0147f34e910fe9abfc533f883f1206e6c7a72d111ff61331254e3ff9f609c16cc81762e15d9ee6c53de36c65ff0"
			}';
	}
	$request = (object) json_decode($raw_data);
	$result = $vendor->Callback($request);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Callback failed: ' . $e->getMessage();
}
