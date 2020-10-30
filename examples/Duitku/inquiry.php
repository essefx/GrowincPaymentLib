<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// $init = new \Growinc\Payment\Init('D6677', '9180265c1850e3ec2286f3b139d4c260');
$init = new \Growinc\Payment\Init('D7129', 'b9fbbcda74f7bdb61ba37abe76ccfd17');
$init->setBaseURI('https://sandbox.duitku.com/webapi/api/merchant');
$init->setRequestURL('https://sandbox.duitku.com/webapi/api/merchant');

$vendor = new \Growinc\Payment\Vendors\Duitku($init);

try {
	$result = $vendor->Inquiry((object) [
			// 'order_id' => '0001285662',
			'order_id' => '2010301604055355913',
		]);
	extract($result);
	print_r($response);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage() . ':' . $e->getCode();
}
