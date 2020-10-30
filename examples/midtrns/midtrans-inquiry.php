<?php

require_once __DIR__ . '/../vendor/autoload.php';

$init = new \Growinc\Payment\Init('SB-Mid-server-4robMkuk3lusaK8mchsScfOM');
$init->setBaseURI('https://api.sandbox.midtrans.com');
$init->setRequestURL('https://api.sandbox.midtrans.com/v2/');

$vendor = new \Growinc\Payment\Vendors\Midtrans($init);
$result = $vendor->Inquiry((object) [
			'order_id' => '0003960969',
		]);
// print_r($result);exit();
try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '0003960969',
		]);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
