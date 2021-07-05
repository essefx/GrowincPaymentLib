<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init(
	// Selaras Devel
	'SB-Mid-client-dVvuSh1E6IUAa_4H', // Client Key
	'SB-Mid-server-0XbM92nnkZGBicN0BM-smsoz' // Server Key
);
$init->setRequestURL('https://api.sandbox.midtrans.com/v2/');

$vendor = new \Growinc\Payment\Vendors\Midtrans($init);
try {
	$result = $vendor->Inquiry((object) [
		'order_id' => '0025325773', // pga order id
		'transaction_id' => '992154fc-66ff-49ea-800d-38c4681e8d3f', // vendor transaction_id
	]);
	extract($result);
	print_r($response);
	/* // Success
	{
		"status": "000",
		"data": {
			"payment_code": "7231279656124571",
			"store": "alfamart",
			"transaction_time": "2021-07-03 22:22:53",
			"gross_amount": "100000.00",
			"currency": "IDR",
			"order_id": "0025325773",
			"payment_type": "cstore",
			"signature_key": "1f3b7204827c0d49a25f46d9a09c4d4c00a1995c385a5577913396ff471ec28f35f9a26cd01b7943dbb34a89b31d4243854581d601d85dd9f18683c6f9953739",
			"status_code": "201",
			"transaction_id": "992154fc-66ff-49ea-800d-38c4681e8d3f",
			"transaction_status": "pending",
			"fraud_status": "accept",
			"status_message": "Success, transaction is found",
			"merchant_id": "G072317714"
		}
	}
	*/
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
