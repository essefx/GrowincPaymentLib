<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('SB-Mid-server-4robMkuk3lusaK8mchsScfOM');
$init->setBaseURI('https://api.sandbox.midtrans.com');
$init->setRequestURL('https://api.sandbox.midtrans.com/v2/');

$vendor = new \Growinc\Payment\Vendors\Midtrans($init);

// $result = $vendor->Inquiry((object) [
// 			'order_id' => '0007395674', //0007405945
// 			'transaction_id' => '4892155a-7181-40ac-9b1e-21a02b337274',
// 		]);
// print_r($result);exit();
try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '0007395674', // pga order id
			'transaction_id' => '96c436a7-fcb4-49eb-a83e-69454db4e99d', // vendor transaction_id
		]);
	// extract($result);
	// print_r($response);
	print_r($result);

} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
