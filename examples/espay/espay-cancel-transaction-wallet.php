<?php

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setRequestURL('https://sandbox-api.espay.id/rest/digitalpay/void');

$transaction = new \Growinc\Payment\Transaction();
// $transaction->setCommcode('SGWGROWINC');
// $transaction->setSignatureKey('ces0bu1jh9qrsakq');
// $transaction->setTransactionRemak('payment for 0008086927 is canceled');
$transaction->setRuuid('INV08101194');
$transaction->setReqDateTime('2020-12-16 13:46:49');
$transaction->setOrderID('0008101194');
$transaction->setTransactionID('ESP1608101205KMY7');
$transaction->setProductCode('OVO');
$transaction->setAmount('180000');
$transaction->setCredentialAttr('ces0bu1jh9qrsakq//Y0F,(5EM=#//SGWGROWINC//VOID');

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->CancelTransactionWallet($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    // extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage() . ':' . $e->getCode();
}