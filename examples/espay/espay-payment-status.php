<?php

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setRequestURL('https://sandbox-api.espay.id/rest/merchant/status');

$transaction = new \Growinc\Payment\Transaction();
// va 4490510956653585
$transaction->setRuuid('INV07665896');
$transaction->setTime('2020-12-11 12:51:41');
$transaction->setOrderID('0007665896');
$transaction->setIsPaymentNotif(''); // Y = will hit Merchant's payment notif URL N = will update trx_status to S in Espay Dashboard Not sent/not filled/filled with "" = standard check payment status
// $transaction->setCommcode('SGWGROWINC');
// $transaction->setSignatureKey('ces0bu1jh9qrsakq');


$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->StatusPayment($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    // extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}