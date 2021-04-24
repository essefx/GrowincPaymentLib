<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('D6677', '9180265c1850e3ec2286f3b139d4c260');
// $init->setBaseURI('https://sandbox.duitku.com/webapi/api/merchant');
$init->setRequestURL('https://sandbox.duitku.com/webapi/api/merchant');

$vendor = new \Growinc\Payment\Vendors\Duitku($init);

// try {
	$result = $vendor->Inquiry((object) [
			'order_id' => $_REQUEST['order_id'] ?? '0001285662',
			// 'order_id' => '202011270347671606452319',
			// 'order_id' => '202011302718391606720920',
		]);
	extract($result);
	print_r($response);
// } catch (\Throwable $e) {
// 	echo 'Inquiry failed: ' . $e->getMessage() . ':' . $e->getCode();
// }
