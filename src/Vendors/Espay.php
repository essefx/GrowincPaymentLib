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
            $this->form['rq_uuid'] = $this->transaction->getRuuid();
            $this->form['rq_datetime'] = $this->transaction->getTime();
            $this->form['order_id'] = $this->transaction->getOrderID();
            $this->form['amount'] = $this->transaction->getAmount();
            $this->form['ccy'] = $this->transaction->getCurrency();
            $this->form['comm_code'] = $this->transaction->getCommcode();
            $this->form['remark1'] = $this->transaction->getCustomerPhone(); // optional
            $this->form['remark2'] = $this->transaction->getCustomerName(); // optional
            $this->form['remark3'] = $this->transaction->getCustomerEmail(); // optional
            $this->form['update'] = $this->transaction->getUpdateOrderId(); // optional
            $this->form['bank_code'] = $this->transaction->getBankCode();
            $this->form['va_expired'] = $this->transaction->getVaExp();
            $this->form['password'] = $this->transaction->getPassword();
            $this->form['signature'] = $this->transaction->getSignature();
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

            // VC    Credit Card (Visa / Master)
            // BK    BCA KlikPay
            // M1    Mandiri Virtual Account
            // BT    Permata Bank Virtual Account
            // A1    ATM Bersama
            // B1    CIMB Niaga Virtual Account
            // I1    BNI Virtual Account
            // VA    Maybank Virtual Account
            // FT    Ritel
            // OV    OVO
            // DN    Indodana Paylater
            // SP    Shopee Pay
            // SA    Shopee Pay Apps
            // AG    Bank Artha Graha
            // S1    Bank Sahabat Sampoerna
            $this->form['payment_method'] = $this->transaction->getPaymentMethod();
            $this->form['payment_url'] = $this->init->getPaymentURL();
            // go
            $this->request['form'] = $this->form;
            $this->request['time'] = $this->transaction->getTime();
            $this->request['url'] = $this->form['payment_url'];
            // amount
            // foreach ($this->form['item_details'] as $price) {
            //     $amountTotal += (int) $price['price'] * (int) $price['quantity'];
            // }
            $this->request['data'] = [
                'rq_uuid' => $this->form['rq_uuid'],
                'rq_datetime' => $this->form['rq_datetime'],
                'order_id' => $this->form['order_id'],
                'amount' => $this->form['amount'],
                'ccy' => $this->form['ccy'],
                'comm_code' => $this->form['comm_code'],
                'remark1' => $this->form['remark1'] ?? '',
                'remark2' => $this->form['remark2'] ?? '',
                'remark3' => $this->form['remark3'] ?? '',
                'update' => $this->form['update'] ?? '',
                'bank_code' => $this->form['bank_code'],
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
            SELF::Validate($request, ['rq_uuid', 'order_id', 'signature']);
            // Go
            // validate in DB
            $data = "0;Success;$request->rq_uuid;$request->order_id;20000.00;IDR;Paymen For Me;$request->rq_datetime";
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
            $this->form['signature'] = $this->transaction->getSignature();
            //
            $this->form['request_url'] = $this->init->getRequestURL();
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
            // return \print_r($this->request);
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
                'reconcile_datetime' => date("Y-M-D h:i:s"),
            ];

            $result = \json_encode($data);

        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }

}