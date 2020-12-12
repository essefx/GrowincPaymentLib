<?php

require_once "../../vendor/autoload.php";

$init = new \Growinc\Payment\Init('66a82db380f34bdfa9b1738eacfb1ac6'); /*(server_key , client_key)*/
$init->setBaseURI('https://sandbox-api.espay.id');

$vendor = new \Growinc\Payment\Vendors\Espay($init);
$raw_data = $_REQUEST ? (object) $_REQUEST : '';
$file = './log/log_inquiry.txt';

try {
    if ($raw_data) {
        $person = json_encode($raw_data, JSON_PRETTY_PRINT);
        file_put_contents($file, $person, FILE_APPEND | LOCK_EX);

        $result = $vendor->Inquiry((object) [
            // 'rq_uuid' => '123ABC-DEF4565',
            // 'rq_datetime' => '2020-08-08 09:17:45',
            // 'sender_id' => 'SGOPLUS',
            // 'receiver_id' => 'SGWGROWINC',
            // 'password' => 'Y0F,(5EM=#', // optional
            // 'comm_code' => 'SGWGROWINC',
            // 'member_id' => '', // optional
            // 'order_id' => '21315',
            // 'signature' => '2df6a64acf77738d9e61a726c04332476bed3738808719b2e987697efe5dbf94',
            /*----*/
            'rq_uuid' => $raw_data->rq_uuid,
            'rq_datetime' => $raw_data->rq_datetime,
            'sender_id' => $raw_data->sender_id ?? '',
            'receiver_id' => $raw_data->receiver_id ?? '',
            'password' => $raw_data->password, // optional
            'comm_code' => $raw_data->comm_code,
            'member_id' => $raw_data->member_id ?? '', // optional
            'order_id' => $raw_data->order_id,
            'signature' => $raw_data->signature,
        ]);
    } else {
        print_r('{data:null}');
    }
    // extract($result);
    // print_r($response);
    if (isset($result)) {print_r($result);}
} catch (\Throwable $e) {
    echo 'Inquiry failed: ' . $e->getMessage();
}