<?php

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setRequestURL('https://sandbox-api.espay.id/rest/merchant/closeinvoice');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setRuuid('INV07665422');
$transaction->setTime('2020-12-11 12:43:47');
$transaction->setOrderID('0007665289');
// $transaction->setCommcode('SGWGROWINC');
// $transaction->setSignatureKey('ces0bu1jh9qrsakq');

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->CloseInvoice($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    // extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage() . ':' . $e->getCode();
}