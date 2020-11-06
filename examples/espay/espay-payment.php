<?php

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setPaymentURL('https://sandbox-api.espay.id/rest/merchantpg/sendinvoice');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setRuuid('123ABC-DEF4565');
$transaction->setTime('2020-08-08 09:17:45');
$transaction->setOrderID('21315');
$transaction->setAmount('20000.00');
$transaction->setCurrency('IDR');
$transaction->setCommcode('SGWGROWINC');
$transaction->setCustomerPhone('081298983535');
$transaction->setCustomerName('Growinc');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setUpdateOrderId('Y'); // Y update data for order id, N new order
$transaction->setBankCode('014');
$transaction->setVaExp('1440'); // minute
$transaction->setPassword('Y0F,(5EM=#');
$transaction->setSignatureKey('ces0bu1jh9qrsakq');
//
$transaction->setMode('SENDINVOICE');
$uppercase = strtoupper('##' . $transaction->getSignatureKey() . '##' . $transaction->getRuuid() . '##' . $transaction->getTime() . '##' . $transaction->getOrderID() . '##' . $transaction->getAmount() . '##' . $transaction->getCurrency() . '##' . $transaction->getCommcode() . '##' . $transaction->getMode() . '##');
$signature = hash('sha256', $uppercase);

$transaction->setSignature($signature);
// $transaction->setKey('S3cr317kEY');
// $transaction->setServicename('SendInvoice');
// $transaction->setMemberid('012');

// $transaction->setCustomerAddress('Jakarta Barat no 52');
$transaction->setCountrycode('IDN');

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->SecurePayment($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    // extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}