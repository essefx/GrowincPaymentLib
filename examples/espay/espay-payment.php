<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

// Growinc
// $merchant_code = 'SGWGROWINC';
// $api_key = '66a82db380f34bdfa9b1738eacfb1ac6';
// $signature = 'ces0bu1jh9qrsakq';
// $password = 'Y0F,(5EM=#';
// Selaras
$merchant_code = 'SGWVOGAME';
$api_key = 'def4e8b9a7c05937db137488858a5b45';
$signature = 'w76g0p75rz07wek7';
$password = '6T,Y@0O3^P';

$init = new \Growinc\Payment\Init($merchant_code, $api_key);

$init->setPaymentURL('https://sandbox-api.espay.id/');

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
$transaction->setCustomerName('LOREM IPSUM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('088812345678');
//
$transaction->setItem('Game 01');
$transaction->setAmount(rand(5000,10000) * 100);
$transaction->setDescription('Product Game Baru');
//
// $transaction->setUpdateOrderId('Y'); // Y update data for order id, N new order\
// $transaction->setCredentialAttr('ces0bu1jh9qrsakq//Y0F,(5EM=#//SGWGROWINC//SENDINVOICE');
$transaction->setPaymentMethod('008'); // bank code
$transaction->setParams([
	'signature' => $signature,
	'password' => $password,
]);

// $item_detail = [
// 	[
//         "id" => "mi-a1",
//         "price" => 60000,
//         "quantity" => 3,
//         "name" => "Redmi 9A",
//         "brand" => "Xiaomi",
//         "category" => "Handphone",
//         "merchant_name" => "Eas Blues Store"
// 	]
// ];
// $transaction->setItem($item_detail);
// $transaction->setDescription('Pembelian Elektronik');
// $transaction->setCountrycode('IDN');

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