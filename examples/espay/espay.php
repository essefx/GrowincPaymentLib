<?php

// $server_key = "SB-Mid-server-4robMkuk3lusaK8mchsScfOM:";
// echo base64_encode($server_key);

// require_once __DIR__ . '../../vendor/autoload.php';
require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setPaymentURL('https://sandbox-api.espay.id/rest/merchantpg/sendinvoice');
// $init->setTokenUrl('https://api.sandbox.espay.com/v2/token');
// $init->setCallbackURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result');
// $init->setReturnURL('https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_form');

$transaction = new \Growinc\Payment\Transaction();
$transaction->setRuuid('123ABC-DEF456');
$transaction->setTime('2020-08-08 09:17:45');
$transaction->setSignature('ces0bu1jh9qrsakq');
$transaction->setCommcode('MYCOMMCODE');
$transaction->setCurrency('IDR');
// $transaction->setKey('S3cr317kEY');
// $transaction->setServicename('SendInvoice');
// $transaction->setOrderID('21313');
// $transaction->setPassword('Y0F,(5EM=#');
// $transaction->setMemberid('012');

$transaction->setCustomerName('LOREM');
$transaction->setCustomerEmail('lorem@ipsum.com');
$transaction->setCustomerPhone('081298983535');
$transaction->setCustomerAddress('Jakarta Barat no 52');
$transaction->setCountrycode('IDN');

$item_detail = [
    ["member_code" => "18032018", "member_name" => "Arief", "amount" => 20000, "total_amount" => "20000", "jumlah_cicilan" => "12", "amount_cicilan" => "2000000", "pelunasan_amount" => 2000000, "description" => "Test Pembayaran VA", "tanggal_penagihan" => "05"], //only cc
];

$transaction->setItem($item_detail);

$vendor = new \Growinc\Payment\Vendors\Espay($init);
// print_r($vendor);
// return;
try {
    $result = $vendor->SecurePayment($transaction); // return payment URL
    // $result = $vendor->RedirectPayment($transaction); // redirect to vendor URL
    // extract($result);
    print_r($result);
    // print_r($vendor->getResponse()); // Get  PSR7 object
} catch (\Throwable $e) {
    echo 'Payment failed: ' . $e->getCode();
}