<?php

// require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y','xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy'); /*(secret_key , public_key)*/
$init->setBaseURI('https://api.xendit.co');
$init->setRequestURL('https://api.xendit.co');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);
// $result = $vendor->Inquiry((object) [
			// 'order_id' => '5f9fb9758d65ab3c1141f230',
		// ]);
// print_r($result);exit();
try {
	$result = $vendor->Inquiry((object) [
			'order_id' => '0007409466', // payment_id (get from Xendit notification)
			'transaction_id' => '5f9fb9758d65ab3c1141f230', // payment_id (get from Xendit notification)
		]);
	// extract($result);
	// print_r($response);
	print_r($result);
} catch (\Throwable $e) {
	echo 'Inquiry failed: ' . $e->getMessage();
}
