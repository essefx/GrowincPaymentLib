<?php

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setRequestURL('https://sandbox-api.espay.id/rest/merchant/status');

$transaction = new \Growinc\Payment\Transaction();
// $transaction->setCommcode('SGWGROWINC');
// $transaction->setSignatureKey('ces0bu1jh9qrsakq');
$transaction->setRuuid('INV08032194');
$transaction->setReqDateTime('2020-12-15 18:36:39');
$transaction->setOrderID('0008032194');
$transaction->setIsPaymentNotif(''); // Y = will hit Merchant's payment notif URL N = will update trx_status to S in Espay Dashboard Not sent/not filled/filled with "" = standard check payment status
$transaction->setCredentialAttr('ces0bu1jh9qrsakq//Y0F,(5EM=#//SGWGROWINC//CHECKSTATUS');


$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->StatusPayment($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}