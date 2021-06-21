<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../vendor/autoload.php';

// Growinc
$merchant_code = 'SGWGROWINC';
$api_key = '66a82db380f34bdfa9b1738eacfb1ac6';
$signature = 'ces0bu1jh9qrsakq';
$password = 'Y0F,(5EM=#';
// Selaras
// $merchant_code = 'SGWVOGAME';
// $api_key = 'def4e8b9a7c05937db137488858a5b45';
// $signature = 'w76g0p75rz07wek7';
// $password = '6T,Y@0O3^P';

$init = new \Growinc\Payment\Init($merchant_code, $api_key);

$init->setRequestURL('https://sandbox-api.espay.id/');

$vendor = new \Growinc\Payment\Vendors\Espay($init);
$raw_data = $_REQUEST ? (object) $_REQUEST : '';
$file = './log/log_inquiry_' . time() . '.txt';

/*
{
    "rq_uuid": "e9b185e4-031d-4794-bc21-1a3282506051",
    "rq_datetime": "2021-06-18 14:14:39",
    "sender_id": "SGOPLUS",
    "receiver_id": "SGWGROWINC",
    "password": "Y0F,(5EM=#",
    "comm_code": "SGWGROWINC",
    "member_code": "",
    "order_id": "0024000477",
    "signature": "2163c0d2b0148d37b8c05630fe8bb39e5fbcbb6ad4303c1c25c66ff7db3337bb"
}
*/

try {
    if ($raw_data) {

        $person = json_encode($raw_data, JSON_PRETTY_PRINT);
        file_put_contents($file, $person, FILE_APPEND | LOCK_EX);

        $result = $vendor->Notification((object) [
            // "rq_uuid": "0553be1e-9ccf-416c-8c70-8b5a280b4a4c",
            // "rq_datetime": "2020-11-06 13:51:47",
            // "sender_id": "SGOPLUS",
            // "receiver_id": "SGWGROWINC",
            // "password": "Y0F,(5EM=#",
            // "comm_code": "SGWGROWINC",
            // "member_code": "4490517454617564",
            // "member_cust_id": "SYSTEM",
            // "member_cust_name": "SYSTEM",
            // "ccy": "IDR",
            // "amount": "21315",
            // "debit_from": "4490517454617564",
            // "debit_from_name": "4490517454617564",
            // "debit_from_bank": "014",
            // "credit_to": "1111111111111",
            // "credit_to_name": "ESPAY AGGREGATOR",
            // "credit_to_bank": "014",
            // "payment_datetime": "2020-11-06 13:51:36",
            // "payment_ref": "ESP16046454717RKG",
            // "payment_remark": "IDR",
            // "order_id": "21315",
            // "product_code": "BCAATM",
            // "product_value": "4490517454617564",
            // "message": "{\"CHANNEL_FLAG\":\"A\"}",
            // "status": "0",
            // "token": "",
            // "total_amount": "25165.00",
            // "tx_key": "ESP16046454717RKG",
            // "fee_type": "B",
            // "tx_fee": "3850.00",
            // "approval_code": "16046454967449721521",
            // "member_id": "4490517454617564",
            // "approval_code_full_bca": "4490517454617564",
            // "signature": "1095d4dfed8d9ba29854903c3fba8be269f75dfb1672688b94ed7dbff19fa03f"
            /*----*/
            'rq_uuid' => $raw_data->rq_uuid,
            'rq_datetime' => $raw_data->rq_datetime,
            'signature' => $raw_data->signature ?? '', // optional
            'signature' => $raw_data->signature,
            'member_id' => $raw_data->member_id, // optional
            'comm_code' => $raw_data->comm_code,
            'order_id' => $raw_data->order_id,
            'ccy' => $raw_data->ccy,
            'debit_from_bank' => $raw_data->debit_from_bank,
            'debit_from' => $raw_data->debit_from ?? '', // optional
            'debit_from_name' => $raw_data->debit_from_name ?? '', // optional
            'credit_to_bank' => $raw_data->credit_to_bank,
            'credit_to' => $raw_data->credit_to ?? '', // optional
            'credit_to_name' => $raw_data->credit_to_name ?? '', // optional
            'product_code' => $raw_data->product_code,
            'message' => $raw_data->message ?? '', // optional
            'payment_datetime' => $raw_data->payment_datetime,
            'payment_ref' => $raw_data->payment_ref,
            'approval_code_full_bca' => $raw_data->approval_code_full_bca ?? '', // optional
            'approval_code_installment_bca' => $raw_data->approval_code_installment_bca ?? '', // optional
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