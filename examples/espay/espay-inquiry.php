<?php

require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('NjI4MTI5ODk4MzUzNTpLb2dpNDkkYjNKYVk='); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');
$init->setPaymentURL('https://sandbox-api.espay.id/b2b/inquiry/name');
$init->setRequestURL('https://sandbox-api.espay.id/rest/merchantpg/sendinvoice');

$vendor = new \Growinc\Payment\Vendors\Espay($init);

try {
    $result = $vendor->Inquiry((object) [
        'rq_uuid' => '123ABC-DEF456',
        'rq_datetime' => '2020-08-08 09:17:45',
        'member_id' => '012',
        'comm_code' => 'MYCOMMCODE',
        'order_id' => '21313',
        'password' => 'Esp1234',
        'signature' => '638f5fd9f590ce81b15570d32edaec03dab1498aa2525947411be6d6e2aa7a2d',
    ]);
    // extract($result);
    // print_r($response);
    print_r($result);
} catch (\Throwable $e) {
    echo 'Inquiry failed: ' . $e->getMessage();
}