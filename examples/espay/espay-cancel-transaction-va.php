<?php

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init(
	'66a82db380f34bdfa9b1738eacfb1ac6'
);
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setRequestURL('https://sandbox-api.espay.id/rest/merchant/updateexpire');

$transaction = new \Growinc\Payment\Transaction();
// $transaction->setCommcode('SGWGROWINC');
// $transaction->setSignatureKey('ces0bu1jh9qrsakq');
$transaction->setRuuid('INV07944483');
$transaction->setReqDateTime('2020-12-14 18:14:49');
$transaction->setOrderID('0007944483');
$transaction->setTransactionRemark('payment for 0007931095 is canceled');
$transaction->setCredentialAttr('ces0bu1jh9qrsakq//Y0F,(5EM=#//SGWGROWINC//EXPIRETRANSACTION');

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->CancelTransaction($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    // extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage() . ':' . $e->getCode();
}