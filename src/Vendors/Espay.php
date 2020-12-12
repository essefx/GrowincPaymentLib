<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Espay extends Requestor implements VendorInterface
{

    protected $form;

    public function Index()
    {
        // Inapplicable
    }

    public function GetToken($args)
    {
        // Inapplicable
    }

    public function CreateDummyForm($args)
    {
        // Inapplicable
    }

    public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
    {
        // Inapplicable
    }

    public function SecurePayment(\Growinc\Payment\Transaction $transaction)
    {
        try {
            $this->transaction = $transaction;
            //
            $this->form['customer_name'] = $this->transaction->getCustomerName();
            $this->form['customer_email'] = $this->transaction->getCustomerEmail();
            $this->form['customer_phone'] = $this->transaction->getCustomerPhone();
            $this->form['customer_address'] = $this->transaction->getCustomerAddress();
            $this->form['country_code'] = $this->transaction->getCountryCode();
            //
            $this->form['billing_address'] = [
                'first_name' => $this->form['customer_name'],
                'last_name' => 'IPSUM',
                'email' => $this->form['customer_email'],
                'phone' => $this->form['customer_phone'],
                'address' => 'sudirman',
                'city' => 'Jakarta',
                'postal_code' => '12190',
                'country_code' => $this->form['country_code'],
            ];
            $this->form['shipping_address'] = [
                'first_name' => $this->form['customer_name'],
                'last_name' => 'IPSUM',
                'email' => $this->form['customer_email'],
                'phone' => $this->form['customer_phone'],
                'address' => 'sudirman',
                'city' => 'Jakarta',
                'postal_code' => '12190',
                'country_code' => $this->form['country_code'],
            ];
            $this->form['customer_details'] = [
                'first_name' => $this->form['customer_name'],
                'last_name' => 'IPSUM',
                'email' => $this->form['customer_email'],
                'phone' => $this->form['customer_phone'],
                'billing_address' => $this->form['billing_address'],
                'shipping_address' => $this->form['shipping_address'],
            ];

            //
            $this->form['rq_uuid'] = $this->transaction->getInvoiceNo();
            $this->form['rq_datetime'] = $this->transaction->getTime();
            $this->form['order_id'] = $this->transaction->getOrderID();
            $this->form['ccy'] = $this->transaction->getCurrency();
            $this->form['comm_code'] = $this->transaction->getCommcode();
            $this->form['remark1'] = $this->transaction->getCustomerPhone(); // optional
            $this->form['remark2'] = $this->transaction->getCustomerName(); // optional
            $this->form['remark3'] = $this->transaction->getCustomerEmail(); // optional
            $this->form['update'] = $this->transaction->getUpdateOrderId(); // optional
            $this->form['va_expired'] = $this->transaction->getExpireAt();
            $this->form['password'] = $this->transaction->getPassword();

            // item details
            $this->form['item_details'] = $this->transaction->getItem();
            $amount_total = 0;
            foreach ($this->form['item_details'] as $price) {
                $amount_total += (int) $price['price'] * (int) $price['quantity'];
            }
            // $this->transaction->setAmount($amount_total);
            $this->form['amount'] = (float) $amount_total.'.00';
            //
            $uppercase = strtoupper('##' . $transaction->getSignatureKey() . '##' . $transaction->getInvoiceNo() 
            . '##' . $transaction->getTime() . '##' . $transaction->getOrderID() . '##' . $amount_total . '##' . 
            $transaction->getCurrency() . '##' . $transaction->getCommcode() . '##' . $transaction->getMode()['sendinvoice'] . '##');
            $signature = hash('sha256', $uppercase);

            $this->form['signature'] = $signature;
            $this->form['description'] = $this->transaction->getDescription();

            $_paymentMethode =  explode(',', $this->transaction->getPaymentMethod());
            $payment_method = $_paymentMethode[0] ?? '';
			$payment_channel = $_paymentMethode[1] ?? '';

            $this->form['payment_type'] = $this->getPayId($_paymentMethode);

            $this->form['payment_url'] = $this->init->getPaymentURL();
            // go
            $this->request['form'] = $this->form;
            $this->request['time'] = $this->transaction->getTime();
            $this->request['url'] = $this->form['payment_url'];

            $this->request['data'] = [
                'rq_uuid' => $this->form['rq_uuid'],
                'rq_datetime' => $this->form['rq_datetime'],
                'order_id' => $this->form['order_id'],
                'amount' => $amount_total,
                'ccy' => $this->form['ccy'],
                'comm_code' => $this->form['comm_code'],
                'remark1' => $this->form['remark1'] ?? '',
                'remark2' => $this->form['remark2'] ?? '',
                'remark3' => $this->form['remark3'] ?? '',
                'update' => $this->form['update'] ?? '',
                'bank_code' => $this->form['payment_type']->id,
                'va_expired' => $this->form['va_expired'],
                'password' => $this->form['password'],
                'signature' => $this->form['signature'],
            ];

            $this->request['headers'] = [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->init->getMID()),
                'Content-Length' => strlen(json_encode($this->request['data'])),
            ];

            $this->request['option'] = [
                'request_opt' => 'json',
            ];

            $post = $this->DoRequest('POST', $this->request);

            $response = (array) $post['response'];

            extract($response);

            if (!empty($status_code) && $status_code === 200) {
                $content = (object) json_decode($content);

                if (!empty($content->error_code)
                    && $content->error_code !== 0000
                ) {
                    // {
                    //     "rq_uuid": "123ABC-DEF4565",
                    //     "rs_datetime": "2020-11-06 10:01:20",
                    //     "error_code": "0000",
                    //     "error_message": "",
                    //     "va_number": "1609583508570383",
                    //     "expired": "2020-11-06 19:48:58",
                    //     "description": "Order ID = 21315 Remark = IDR",
                    //     "total_amount": "25815.00",
                    //     "amount": "21315",
                    //     "fee": "4500.00",
                    //     "bank_code": "002"
                    // }
                    $content = [
                        'status' => '0000',
                        'data' => (array) $content,
                    ];

                    $result = [
                        'request' => (array) $this->request,
                        'response' => [
                            'content' => json_encode($content),
                            'status_code' => 200,
                            'va_number' => $content['data']['va_number'],
                            'bank_code' => $content['data']['bank_code'],
                            'amount' => $content['data']['total_amount'],
                            'transaction_id' => '', // vendor transaction_id
                            'order_id' => $this->form['order_id'], // PGA order_id
                            'payment_type' => $payment_method, 
                            'transaction_status' => 'In Process',
                        ],
                    ];

                } else {
                    throw new \Exception($content->error_message);
                }
            } else {
                throw new \Exception($content);
            }

        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }

    public function Callback(object $request)
    {
        // Inapplicable
    }

    public function CallbackAlt(object $request)
    {
        // Inapplicable
    }

    public function Inquiry(object $request)
    {
        try {
            SELF::Validate($request, ['order_id', 'signature']);
            // Go
            // validate in DB
            $data = "0;Success;$request->order_id;180000.00;IDR;Paymen For $request->order_id;$request->rq_datetime"; //
            $result = $data;

        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }

    public function Cancel(object $request)
    {
        // Inapplicable
    }

    public function Settle(object $request)
    {
        // Inapplicable
    }

    public function Refund(object $request)
    {
        // Inapplicable
    }

    public function RefundStatus(object $request)
    {
        // Inapplicable
    }

    public function StatusPayment(\Growinc\Payment\Transaction $transaction)
    {
        try {
            $this->transaction = $transaction;
            //
            $this->form['uuid'] = $this->transaction->getRuuid();
            $this->form['rq_datetime'] = $this->transaction->getTime();
            $this->form['comm_code'] = $this->transaction->getCommcode();
            $this->form['order_id'] = $this->transaction->getOrderID();
            $this->form['is_paymentnotif'] = $this->transaction->getIsPaymentNotif(); // optional
            //
            $this->form['request_url'] = $this->init->getRequestURL();

            $uppercase = strtoupper('##' . $transaction->getSignatureKey() . '##' . $transaction->getTime() . '##' . $transaction->getOrderID() . '##' . $transaction->getMode()['checkstatus'] . '##');
            $signature = hash('sha256', $uppercase);

            $this->form['signature'] = $signature;
            // go
            $this->request['form'] = $this->form;
            $this->request['time'] = $this->transaction->getTime();
            $this->request['url'] = $this->form['request_url'];

            $this->request['data'] = [
                'uuid' => $this->form['uuid'],
                'rq_datetime' => $this->form['rq_datetime'],
                'comm_code' => $this->form['comm_code'],
                'order_id' => $this->form['order_id'],
                'is_paymentnotif' => $this->form['is_paymentnotif'],
                'signature' => $this->form['signature'],
            ];

            $this->request['headers'] = [
                'Content-Type' => 'application/x-www-form-urlencoded',
                // 'Content-Type' => 'application/json',
                // 'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->init->getMID()),
                'Content-Length' => strlen(json_encode($this->request['data'])),
            ];

            $this->request['option'] = [
                'request_opt' => 'json',
            ];

            $post = $this->DoRequest('POST', $this->request);

            $response = (array) $post['response'];
            extract($response);

            if (!empty($status_code) && $status_code === 200) {
                $content = (object) json_decode($content);
                if (!empty($content->error_code) && ($content->error_code == 0000)) {
                    //  {
                    //     "rq_uuid": "123ABC-DEF4565",
                    //     "rs_datetime": "2020-11-06 12:12:01",
                    //     "error_code": "0000",
                    //     "error_message": "",
                    //     "comm_code": "SGWGROWINC",
                    //     "member_code": null,
                    //     "tx_id": "ESP1604580538534C",
                    //     "order_id": "21315",
                    //     "ccy_id": "IDR",
                    //     "amount": "21315",
                    //     "tx_status": "IP",
                    //     "tx_reason": "",
                    //     "tx_date": "2020-11-05",
                    //     "created": "2020-11-05 19:48:56",
                    //     "expired": "2020-11-06 19:48:58",
                    //     "bank_name": "BANK BRI",
                    //     "product_name": "BRI VA",
                    //     "product_value": "",
                    //     "payment_ref": "",
                    //     "merchant_code": "",
                    //     "token": "",
                    //     "member_cust_id": "SYSTEM",
                    //     "member_cust_name": "SYSTEM",
                    //     "debit_from_name": "",
                    //     "debit_from_bank": "002",
                    //     "credit_to": "1111111111111",
                    //     "credit_to_name": "1111111111111",
                    //     "credit_to_bank": "002",
                    //     "payment_datetime": "2020-11-06 11:32:21"
                    // }

                    $content = [
                        'status' => '0000',
                        'data' => (array) $content,
                    ];


                    $ava_tx_status = [
                        'SP' => 'Suspect',
                        'IP' => 'In Process',
                        'F' => 'Failed',
                        'S' => 'Success' 
                    ];
                    $status = $ava_tx_status[$content['data']['tx_status']] ?? $content['data']['tx_status'];

                    $result = [
                        'request' => (array) $this->request,
                        'response' => [
                            'content' => json_encode($content),
                            'status_code' => 200,
                            'order_id' => $this->form['order_id'], // PGA order_id
                            'transaction_id' => $content['data']['tx_id'], // vendor transaction_id
                            'status' => $status,
                            'transaction_time' => $content['data']['payment_datetime'],
                            'amount' => $content['data']['amount'],
                            'bank_code' => $content['data']['debit_from_bank'] ?? $content['data']['credit_to_bank'] ?? '',
                            'va_number' => $content['data']['member_code'] ?? $content['data']['product_value'] ?? $content['data']['debit_from_name'] ?? '',
                        ],
                    ];

                } else {
                    throw new \Exception($content->error_message);
                }
            } else {
                throw new \Exception($content);
            }

        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }

    public function Notification(object $request)
    {
        try {
            SELF::Validate($request, [
                'rq_uuid',
                'rq_datetime',
                'order_id',
                'signature',
            ]);
            // Go
            // validate in DB
            // success_flag,error message,reconcile_id , order_id,reconcile_datetime

            // $data = "0;Success;236347301;$request->order_id;$request->rq_datetime";
            // $result = $data;

            $data = [
                'rq_uuid' => $request->order_id,
                'rs_datetime' => $request->rq_datetime,
                'error_code' => '201',
                'error_message' => 'FAILED',
                'signature' => $request->signature,
                'order_id' => $request->order_id,
                'reconcile_id' => 'INF' . rand(6, 9),
                'reconcile_datetime' => date("Y-m-d h:i:s"), // date("Y-M-D h:i:s"),
            ];

            $result = \json_encode($data);

        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }

    public function CloseInvoice(\Growinc\Payment\Transaction $transaction)
    {
        try {
            $this->transaction = $transaction;
            //
            $this->form['uuid'] = $this->transaction->getRuuid();
            $this->form['rq_datetime'] = $this->transaction->getTime();
            $this->form['comm_code'] = $this->transaction->getCommcode();
            $this->form['order_id'] = $this->transaction->getOrderID();
            //
            $this->form['request_url'] = $this->init->getRequestURL();

            $uppercase = strtoupper('##' . $transaction->getSignatureKey() . '##' . $transaction->getRuuid() . '##' . 
            $transaction->getTime() . '##' . $transaction->getOrderID() . '##' . $transaction->getCommcode() . '##' . $transaction->getMode()['closeinvoice'] .'##');
            $signature = hash('sha256', $uppercase);

            $this->form['signature'] = $signature;
            // go
            $this->request['form'] = $this->form;
            $this->request['time'] = $this->transaction->getTime();
            $this->request['url'] = $this->form['request_url'];

            $this->request['data'] = [
                'uuid' => $this->form['uuid'],
                'rq_datetime' => $this->form['rq_datetime'],
                'comm_code' => $this->form['comm_code'],
                'order_id' => $this->form['order_id'],
                'signature' => $this->form['signature'],
            ];

            $this->request['headers'] = [
                'Content-Type' => 'application/x-www-form-urlencoded',
                // 'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->init->getMID()),
                'Content-Length' => strlen(json_encode($this->request['data'])),
            ];

            $this->request['option'] = [
                'request_opt' => 'json',
            ];
            // return \print_r($this->request['data']);
            $post = $this->DoRequest('POST', $this->request);
           
            $response = (array) $post['response'];
            extract($response);
            $content = (object) json_decode($content);
 
            if (!empty($status_code) && $status_code === 200) {
                if (!empty($content->error_code) && ($content->error_code == 0000)) {
                    $content = [
                        'status' => '0000',
                        'data' => (array) $content,
                    ];

                    $result = [
                        'request' => (array) $this->request,
                        'response' => [
                            'content' => json_encode($content),
                            'status_code' => 200,
                        ],
                    ];

                } else {
                    throw new \Exception($content->error_message);
                }
            } else {
                throw new \Exception($content);
            }

        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }

    // 
    public function getPayId($paymentId){
		switch ($paymentId[0]) {
                /* Bank Transfer */ 
                case 'bank_transfer':      
                    switch ($paymentId[1]) {
                        case 'bca':
                            $id = '014';
                            $name = 'BCAATM';
                        break;
                        case 'bni':
                            $id = '009';
                            $name = 'BNIATM';
                        break;
                        case 'bptn':
                            $id = '075';
                            $name = 'BPTN';
                        break;
                        case 'bri':
                            $id = '002';
                            $name = 'BRIATM';
                        break;
                        case 'btpn':
                            $id = '213';
                            $name = 'BTPNWOW';
                        break;
                        case 'cimb':
                            $id = '022';
                            $name = 'CIMBATM';
                        break;
                        case 'danamon':
                            $id = '011';
                            $name = 'DANAMONATM';
                        break;
                        case 'dbs':
                            $id = '046';
                            $name = 'DBSATM';
                        break;
                        case 'mandiri':
                            $id = '008';
                            $name = 'MANDIRIATM';
                        break;
                        case 'mandiri_syariah':
                            $id = '451';
                            $name = 'MANDIRISYARIAHATM';
                        break;
                        case 'maspion':
                            $id = '157';
                            $name = 'MASPIONATM';
                        break;
                        case 'maybank':
                            $id = '016';
                            $name = 'MAYBANK';
                        break;
                        case 'permata':
                            $id = '013';
                            $name = 'PERMATAATM';
                        break;
                        default:
                            $id = '014';
                            $name = 'BCAATM';
                        break;
                    }
                break;

                /* Bank Transfer */ 
                case 'internet_banking':
                    switch ($paymentId[1]) {
                        case 'bca':
                            $id = '014';
                            $name = 'BCA VA Online';
                        break;
                        case 'bni':
                            $id = '009';
                            $name = 'BNI VA';
                        break;
                        case 'bptn':
                            $id = '075';
                            $name = 'Jenius IB';
                        break;
                        case 'bri':
                            $id = '002';
                            $name = 'BRI VA';
                        break;
                        case 'cimb':
                            $id = '022';
                            $name = 'VA CIMB Niaga';
                        break;
                        case 'danamon':
                            $id = '011';
                            $name = 'Danamon Online Banking';
                        break;
                        case 'dbs':
                            $id = '046';
                            $name = 'DBS VA';
                        break;
                        case 'mandiri':
                            $id = '008';
                            $name = 'MANDIRI VA';
                        break;
                        case 'mandiri_syariah':
                            $id = '451';
                            $name = 'Mandiri Syariah VA';
                        break;
                        case 'maspion':
                            $id = '157';
                            $name = 'VA MASPION';
                        break;
                        case 'maybank':
                            $id = '016';
                            $name = 'MAYBANK va';
                        break;
                        case 'permata':
                            $id = '013';
                            $name = 'PERMATA VA';
                        break;
                        default:
                            $id = '014';
                            $name = 'BCAATM';
                        break;
                    }
                break;
            
            /* Virtual */ 
            case 'virtual':
                switch ($paymentId[2]) {
                    case 'gopay':
                        $id = '014';
                        $name = 'GOPAY';
                    break;
                    case 'credivo':
                        $id = '014';
                        $name = 'KREDIVO';
                    break;       
                    case 'tcash':
                        $id = '008';
                        $name = 'TCASH';
                    break;
                    case 'indomaret':
                        $id = '066';
                        $name = 'INDOMARET';
                    break;
                    case 'alfa':
                        $id = '026';
                        $name = 'ALFA';
                    break;
                    case 'dana':
                        $id = '008';
                        $name = 'DANA';
                    break;
                    case 'link_aja':
                        $id = '008';
                        $name = 'Link Aja';
                    break;
                    case 'shoppe_pay':
                        $id = '008';
                        $name = 'SHOPEEPAY JUMPAPP';
                    break;
                    case 'saldomu':
                        $id = '008';
                        $name = 'SALDOMU';
                    break;
                    case 'jenius':
                        $id = '075';
                        $name = 'JENIUSIB';
                    break;
                    case 'ovo':
                        $id = '503';
                        $name = 'OVO';
                    break;
                    case 'paypal':
                        $id = '717';
                        $name = 'PAYPAL';
                    break;
                    default:
                        $id = '503';
                        $name = 'OVO';
                    break;
                }
            break;

            // case 'credit_card':
            // 	switch ($paymentId[3]) {
            // 		case 'ccinstall3':
            // 			$id = '008';
            // 			$name = 'CCINSTALL3';
            // 		break;
            // 		case 'ccinstal12':
            // 			$id = '008';
            // 			$name = 'CCINSTALL12';
            // 		break;
            // 		case 'visa_master':
            // 			$id = '008';
            // 			$name = 'Credit Card Visa / Master';
            //         break;
            //         case 'ccv_promotion':
            // 			$id = '008';
            // 			$name = 'CCPROMO';
            // 		break;
            // 		case 'ccinstall6':
            // 			$id = '008';
            // 			$name = 'CCINSTALL6';
            // 		break;
            // 	}
            // break;
        }
        return (object) ['id' => $id, 'name' => $name];
    }

}