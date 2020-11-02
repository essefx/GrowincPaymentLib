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
        $this->request['headers'] = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "BASIC " . $args['token_url'],
        ];
        $this->request['url'] = $args['token_url'];
        $this->request['data'] = [
            'client_key' => $args['client_key'],
            'card_number' => $args['card_number'],
            'card_exp_month' => $args['card_exp_month'],
            'card_exp_year' => $args['card_exp_year'],
            'card_cvv' => $args['card_cvv'],
        ];
        $get = $this->DoRequest('GET', $this->request);

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
            $this->form['reques_Identifier'] = $this->transaction->getRuuid();
            $this->form['time'] = $this->transaction->getTime();
            $this->form['signature'] = $this->transaction->getSignature();
            $this->form['comunityCode'] = $this->transaction->getCommcode();
            $this->form['description'] = $this->transaction->getDescription();
            $this->form['currency'] = $this->transaction->getCurrency();
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

            $this->form['item_details'] = $this->transaction->getItem();
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
            $this->form['payment_type'] = $this->transaction->getPaymentType();
            $this->form['payment_url'] = $this->init->getPaymentURL() . '/v2/charge';
            $this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes

            $this->form['return_url'] = $this->init->getReturnURL();
            // go
            $this->request['form'] = $this->form;
            $this->request['time'] = $this->transaction->getTime();
            $this->request['url'] = $this->form['payment_url'];
            // amount
            // foreach ($this->form['item_details'] as $price) {
            //     $amountTotal += (int) $price['price'] * (int) $price['quantity'];
            // }
            $this->request['data'] = [
                'rq_uuid' => $this->form['reques_Identifier'],
                'rq_datetime' => $this->form['time'],
                'signature' => $this->form['signature'],
                'comm_code' => $this->form['comunityCode'],
                'ccy' => $this->form['currency'],
                'invoices' => [
                    $this->form['description'] => [
                        'member_code' => $this->form['item_details'][0]['member_code'],
                        'member_name' => $this->form['item_details'][0]['member_name'],
                        'amount' => $this->form['item_details'][0]['amount'],
                        'total_amount' => $this->form['item_details'][0]['total_amount'],
                        'number_of_installment' => $this->form['item_details'][0]['jumlah_cicilan'],
                        'installment_amount' => $this->form['item_details'][0]['amount_cicilan'],
                        'balance_payment_amount' => $this->form['item_details'][0]['pelunasan_amount'],
                        'description' => $this->form['item_details'][0]['description'],
                        'billing_date' => $this->form['item_details'][0]['tanggal_penagihan'],
                    ],
                ],
            ];

            $this->request['headers'] = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
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
                return print_r($content);
                if (!empty($content->status_code)
                    && $content->status_code == 201
                ) {
                    $content = [
                        'status' => '000',
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
                    throw new \Exception($content->status_message);
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
        return ['fd' => 'wkwk'];
    }

    public function CallbackAlt(object $request)
    {
        // Inapplicable
    }

    public function Inquiry(object $request)
    {
        try {
            SELF::Validate($request, ['order_id', 'rq_uuid', 'rq_datetime', 'member_id', 'comm_code', 'password', 'signature']);
            // Go
            $this->request['time'] = time();
            $this->request['url'] = $this->init->getRequestURL();
            $this->request['data'] = [
                'rq_uuid' => $request->rq_uuid,
                'rq_datetime' => $request->rq_datetime,
                'member_id' => $request->member_id,
                'comm_code' => $request->comm_code,
                'order_id' => $request->order_id,
                'password' => $request->password,
                'signature' => $request->signature,
            ];
            $this->request['headers'] = [
                'Content-Type' => 'application/json',
                'Content-Length' => strlen(json_encode($this->request['data'])),
            ];
            $this->request['option'] = [
                'to_json' => false,
            ];
            $post = $this->DoRequest('POST', $this->request);
            $response = (array) $post['response'];
            extract($response);
            if (!empty($status_code) && $status_code === 200) {
                $content = (object) json_decode($content);
                if (!empty($content->status_code)
                    && $content->status_code == "201"
                    && $content->order_id == $request->order_id
                ) {
                    // Success
                    /*
                    {
                    "merchantOrderId": "0001297441",
                    "reference": "D6677KW403DFH8VOFMRJ",
                    "amount": "100000",
                    "fee": "0.00",
                    "statusCode": "00",
                    "statusMessage": "SUCCESS"
                    }
                     */
                    $content = [
                        'status' => '000',
                        'data' => (array) $content,
                    ];
                    $result = [
                        'request' => (array) $request,
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

    public function VirtualAccount(object $argc)
    {
        $this->request['headers'] = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => "BASIC " . $args['token_url'],
        ];
        $this->request['url'] = $args['token_url'];
        $this->request['data'] = [
            'rq_uuid' => $args['rq_uuid'],
            'card_number' => $args['card_number'],
            'rq_datetime' => $args['rq_datetime'],
            'order_id' => $args['order_id'],
            'ccy' => $args['ccy'],
            'comm_code' => $args['comm_code'],
            'remark1' => $args['remark1'],
            'remark2' => $args['remark2'],
            'remark3' => $args['remark3'],
            'update' => $args['update'],
            'bank_code' => $args['bank_code'],
            'va_expired' => $args['va_expired'],
            'amount' => $args['amount'],
            'signature' => $args['signature'],
        ];
        $get = $this->DoRequest('GET', $this->request);
    }

}