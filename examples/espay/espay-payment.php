<?php

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setPaymentURL('https://sandbox-api.espay.id/rest/merchantpg/sendinvoice');

$transaction = new \Growinc\Payment\Transaction();
// $transaction->setTime('2020-11-07 11:17:45'); // 
// $transaction->setOrderID('21315');
// $transaction->setCurrency('IDR');
// $transaction->setVaExp('1440'); // minute
// $transaction->setRuuid('123A-DEF4-1214');
// $transaction->setAmount('20000.00');
// $transaction->setCommcode('SGWGROWINC');
// $transaction->setPassword('Y0F,(5EM=#');
// $transaction->setSignatureKey('ces0bu1jh9qrsakq');
// $transaction->setMode('SENDINVOICE');

$transaction->setCustomerPhone('081298983535');
$transaction->setCustomerName('Growinc');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setUpdateOrderId('Y'); // Y update data for order id, N new order
$transaction->setPaymentMethod('bank_transfer,bca'); // bank code

$item_detail = [
	[
        "id" => "mi-a1",
        "price" => 60000,
        "quantity" => 3,
        "name" => "Redmi 9A",
        "brand" => "Xiaomi",
        "category" => "Handphone",
        "merchant_name" => "eas_blues-store"
	] // only cc
];
$transaction->setItem($item_detail);
$transaction->setDescription('Pembelian Elektronik');
$transaction->setCountrycode('IDN');

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->SecurePayment($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getMessage() . ':' . $e->getCode();
}