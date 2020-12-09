<?php 

// require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$init = new \Growinc\Payment\Init('xnd_development_8OoTlgwIthHrTr7R9gg0AIUhH2PAAPjAdReAltPc7yQxzBlRnhAmYwdGqn6vG4Y','xnd_public_development_UmKcL9LSSd96GKqb7ZN3UNZIPNUClJIBd4ndBoqFPRpqslgJ5q7GzCV0lWMOXRZy'); /*(secret_key , public_key)*/
$init->setBaseURI('https://api.xendit.co');
$init->setPaymentURL('https://api.xendit.co');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setAmount(100000);
$transaction->setCountrycode('IDN');
// $transaction->setVaName('Lorem Ipsum');

/*	VA
	BCA , MANDIRI , BNI , BRI , PERMATA
*/
// xendit param required
$transaction->setPaymentMethod('bca'); // for VA 
$transaction->setPaymentType('bank_transfer');
$transaction->setDescription('Pembelian Elektronik');

$vendor = new \Growinc\Payment\Vendors\Xendit($init);
// $result = $vendor->SecurePayment($transaction);
// print_r($result);exit();
 
try {
	$result = $vendor->SecurePayment($transaction); // return payment URL
	// extract($result);
	// print_r($response);
	print_r($result);
	//
	// print_r($vendor->getRequest());
	// print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
	echo 'Payment failed: ' . $e->getCode();
}
  

?>    