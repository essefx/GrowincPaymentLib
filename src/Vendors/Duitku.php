<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Duitku extends Requestor implements VendorInterface
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

<<<<<<< Updated upstream
	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['amount'] = $this->transaction->getAmount();
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
					'firstName' => $this->form['customer_name'],
					'lastName' => '',
					'address' => $this->form['customer_address'],
					'city' => '',
					'postalCode' => '',
					'phone' => $this->form['customer_phone'],
					'countryCode' => $this->form['country_code'],
				];
			$this->form['shipping_address'] = [
					'firstName' => $this->form['customer_name'],
					'lastName' => '',
					'address' => $this->form['customer_address'],
					'city' => '',
					'postalCode' => '',
					'phone' => $this->form['customer_phone'],
					'countryCode' => $this->form['country_code'],
				];
			$this->form['customer_details'] = [
					'firstName' => $this->form['customer_name'],
					'lastName' => '',
					'email' => $this->form['customer_email'],
					'phoneNumber' => $this->form['customer_phone'],
					'billingAddress' => $this->form['billing_address'],
					'shippingAddress' => $this->form['shipping_address'],
				];
			// VC	Credit Card (Visa / Master)
			// BK	BCA KlikPay
			// M1	Mandiri Virtual Account
			// BT	Permata Bank Virtual Account
			// A1	ATM Bersama
			// B1	CIMB Niaga Virtual Account
			// I1	BNI Virtual Account
			// VA	Maybank Virtual Account
			// FT	Ritel
			// OV	OVO
			// DN	Indodana Paylater
			// SP	Shopee Pay
			// SA	Shopee Pay Apps
			// AG	Bank Artha Graha
			// S1	Bank Sahabat Sampoerna
			$this->form['payment_method'] = $this->transaction->getPaymentMethod();
			$this->form['payment_url'] = $this->init->getPaymentURL() . '/v2/inquiry';
			$this->form['callback_url'] = $this->init->getCallbackURL();
			// Redirect
			// merchantOrderId: Nomor transaksi dari merchant abcde12345
			// reference: Nomor referensi transaksi dari Duitku. Mohon disimpan untuk keperluan pencatatan atau pelacakan transaksi. d011111
			// resultCode: Hasil status transaksi
			// 00 - Success
			// 01 - Pending
			// 02 - Canceled
			$this->form['return_url'] = $this->init->getReturnURL();
			// Request argseter
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
			$this->form['signature'] = md5(
					$this->init->getMID() .
					$this->form['order_id'] .
					(float) $this->form['amount'] .
					$this->init->getSecret()
				);
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];
			$this->request['data'] = [
					'merchantCode' => $this->init->getMID(),
					'paymentAmount' => $this->form['amount'],
					'paymentMethod' => $this->form['payment_method'],
					'merchantOrderId' => $this->form['order_id'],
					'productDetails' => $this->form['description'],
					'additionalargs' => '', // optional
					'merchantUserInfo' => '', // optional
					'customerVaName' => $this->form['customer_name'],
					'email' => $this->form['customer_email'],
					'phoneNumber' => $this->form['customer_phone'],
					'itemDetails' => [],
					'customerDetail' => $this->form['customer_details'],
					'callbackUrl' => $this->form['callback_url'],
					'returnUrl' => $this->form['return_url'],
					'signature' => $this->form['signature'],
					'expiryPeriod' => $this->form['expiry_period'],
				];
			$this->request['headers'] = [[
					'Content-Type' => 'application/json',
					'Content-Length' => strlen(json_encode($this->request['data'])),
				]];
			$this->request['option'] = [
					'to_json' => true,
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (	!empty($content->statusMessage)
						&& $content->statusMessage == "SUCCESS"
				) {
					// Success
					/*
					{
						"merchantCode": "D6677",
						"reference": "D66774XLQY7KUDPP0WGA",
						"paymentUrl": "http://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=B1Q3XQMO6CY44R7F2",
						"vaNumber": "11990140616361",
						"amount": "100000",
						"statusCode": "00",
						"statusMessage": "SUCCESS"
					}
					*/
					$res = [
							'status' => '000',
							'data' => (array) $content,
						];
				} else {
					// throw new \Exception($content->statusMessage);
					// Other status
					/*
					*/
					$res = [
							'status' => str_pad($content->statusCode, 3, '0', STR_PAD_LEFT),
							'data' => (array) $content,
						];
				}
				$return = [
						'request' => (array) $this->request,
						'response' => [
								'content' => json_encode($res),
								'status_code' => 200,
							],
					];
			} else {
				throw new \Exception($content);
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $return ?? [];
	}

	public function Callback(object $request)
	{
		// Example incoming data
		/*
		{
			"merchantCode": "D6677",
			"amount": "100000",
			"merchantOrderId": "0001285662",
			"productDetail": "Payment for order 0001285662",
			"additionalParam": null,
			"resultCode": "00",
			"signature": "439030a6da086ee13558137f07d4a27d",
			"paymentCode": "VC",
			"merchantUserId": null,
			"reference": "D6677JXVYL752HMAV0AD"
		}
		*/
		try {
			SELF::Validate($request, ['amount', 'merchantOrderId', 'signature']);
			$signature = md5(
					$this->init->getMID() .
					(float) $request->amount .
					$request->merchantOrderId .
					$this->init->getSecret()
				);
			if (strcmp($signature, $request->signature) === 0) {
				$res = [
						'status' => '000',
						'data' => (array) $request,
					];
				$return = [
						'request' => (array) $request,
						'response' => [
								'content' => json_encode($res),
								'status_code' => 200,
							],
					];
			} else {
				throw new \Exception('Signature check failed');
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $return ?? [];
	}
=======
    public function SecurePayment(\Growinc\Payment\Transaction $transaction)
    {
        try {
            $this->transaction = $transaction;
            //
            $this->form['order_id'] = $this->transaction->getOrderID();
            $this->form['invoice_no'] = $this->transaction->getInvoiceNo();
            $this->form['amount'] = $this->transaction->getAmount();
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
                'firstName' => $this->form['customer_name'],
                'lastName' => '',
                'address' => $this->form['customer_address'],
                'city' => '',
                'postalCode' => '',
                'phone' => $this->form['customer_phone'],
                'countryCode' => $this->form['country_code'],
            ];
            $this->form['shipping_address'] = [
                'firstName' => $this->form['customer_name'],
                'lastName' => '',
                'address' => $this->form['customer_address'],
                'city' => '',
                'postalCode' => '',
                'phone' => $this->form['customer_phone'],
                'countryCode' => $this->form['country_code'],
            ];
            $this->form['customer_details'] = [
                'firstName' => $this->form['customer_name'],
                'lastName' => '',
                'email' => $this->form['customer_email'],
                'phoneNumber' => $this->form['customer_phone'],
                'billingAddress' => $this->form['billing_address'],
                'shippingAddress' => $this->form['shipping_address'],
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
            $this->form['payment_url'] = $this->init->getPaymentURL() . '/v2/inquiry';
            $this->form['callback_url'] = $this->init->getCallbackURL();
            // Redirect
            // merchantOrderId: Nomor transaksi dari merchant abcde12345
            // reference: Nomor referensi transaksi dari Duitku. Mohon disimpan untuk keperluan pencatatan atau pelacakan transaksi. d011111
            // resultCode: Hasil status transaksi
            // 00 - Success
            // 01 - Pending
            // 02 - Canceled
            $this->form['return_url'] = $this->init->getReturnURL();
            // Request argseter
            $this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
            $this->form['signature'] = md5(
                $this->init->getMID() .
                $this->form['order_id'] .
                (float) $this->form['amount'] .
                $this->init->getSecret()
            );
            // Go
            $this->request['form'] = $this->form;
            $this->request['time'] = $this->transaction->getTime();
            $this->request['url'] = $this->form['payment_url'];
            $this->request['data'] = [
                'merchantCode' => $this->init->getMID(),
                'paymentAmount' => $this->form['amount'],
                'paymentMethod' => $this->form['payment_method'],
                'merchantOrderId' => $this->form['order_id'],
                'productDetails' => $this->form['description'],
                'additionalargs' => '', // optional
                'merchantUserInfo' => '', // optional
                'customerVaName' => $this->form['customer_name'],
                'email' => $this->form['customer_email'],
                'phoneNumber' => $this->form['customer_phone'],
                'itemDetails' => [],
                'customerDetail' => $this->form['customer_details'],
                'callbackUrl' => $this->form['callback_url'],
                'returnUrl' => $this->form['return_url'],
                'signature' => $this->form['signature'],
                'expiryPeriod' => $this->form['expiry_period'],
            ];
            $this->request['headers'] = [
                'Content-Type' => 'application/json',
                'Content-Length' => strlen(json_encode($this->request['data'])),
            ];
            $post = $this->DoRequest('POST', $this->request);
            $response = (array) $post['response'];
            extract($response);
            if (!empty($status_code) && $status_code === 200) {
                $content = (object) json_decode($content);
                if (!empty($content->statusMessage)
                    && $content->statusMessage == "SUCCESS"
                ) {
                    // Success
                    /*
                    {
                    "merchantCode": "D6677",
                    "reference": "D66774XLQY7KUDPP0WGA",
                    "paymentUrl": "http://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=B1Q3XQMO6CY44R7F2",
                    "vaNumber": "11990140616361",
                    "amount": "100000",
                    "statusCode": "00",
                    "statusMessage": "SUCCESS"
                    }
                     */
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
                    throw new \Exception($content->statusMessage);
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
        // Example incoming data
        /*
        {
        "merchantCode": "D6677",
        "amount": "100000",
        "merchantOrderId": "0001285662",
        "productDetail": "Payment for order 0001285662",
        "additionalParam": null,
        "resultCode": "00",
        "signature": "439030a6da086ee13558137f07d4a27d",
        "paymentCode": "VC",
        "merchantUserId": null,
        "reference": "D6677JXVYL752HMAV0AD"
        }
         */
        try {
            SELF::Validate($request, ['amount', 'merchantOrderId', 'signature']);
            $signature = md5(
                $this->init->getMID() .
                (float) $request->amount .
                $request->merchantOrderId .
                $this->init->getSecret()
            );
            if (strcmp($signature, $request->signature) === 0) {
                $content = [
                    'status' => '000',
                    'data' => (array) $request,
                ];
                $result = [
                    'request' => (array) $request,
                    'response' => [
                        'content' => json_encode($content),
                        'status_code' => 200,
                    ],
                ];
            } else {
                throw new \Exception('Signature check failed');
            }
        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }
>>>>>>> Stashed changes

    public function CallbackAlt(object $request)
    {
        // Inapplicable
    }

<<<<<<< Updated upstream
	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, ['order_id']);
			$signature = md5(
					$this->init->getMID() .
					$request->order_id .
					$this->init->getSecret()
				);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL() . '/transactionStatus';
			$this->request['data'] = [
					'merchantCode' => $this->init->getMID(),
					'merchantOrderId' => $request->order_id,
					'signature' => $signature,
				];
			$this->request['headers'] = [[
					'Content-Type' => 'application/json',
					'Content-Length' => strlen(json_encode($this->request['data'])),
				]];
			$this->request['option'] = [
					'to_json' => false,
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (	!empty($content->statusMessage)
						&& $content->statusMessage == "SUCCESS"
						&& $content->merchantOrderId == $request->order_id
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
					$res = [
							'status' => '000',
							'data' => (array) $content,
						];
				} else {
					// throw new \Exception($content->statusMessage);
					// Other status
					/*
					{
						"merchantOrderId": "2010301604055355913",
						"reference": "D7129VXO1GMATZCJJXMX",
						"amount": "19405",
						"fee": "4000.00",
						"statusCode": "01",
						"statusMessage": "PROCESS"
					}
					*/
					$res = [
							'status' => str_pad($content->statusCode, 3, '0', STR_PAD_LEFT),
							'data' => (array) $content,
						];
				}
				$return = [
						'request' => (array) $this->request,
						'response' => [
								'content' => json_encode($res),
								'status_code' => 200,
							],
					];
			} else {
				throw new \Exception($content);
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $return ?? [];
	}
=======
    public function Inquiry(object $request)
    {
        try {
            SELF::Validate($request, ['order_id']);
            $signature = md5(
                $this->init->getMID() .
                $request->order_id .
                $this->init->getSecret()
            );
            // Go
            $this->request['time'] = time();
            $this->request['url'] = $this->init->getRequestURL() . '/transactionStatus';
            $this->request['data'] = [
                'merchantCode' => $this->init->getMID(),
                'merchantOrderId' => $request->order_id,
                'signature' => $signature,
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
                if (!empty($content->statusMessage)
                    && $content->statusMessage == "SUCCESS"
                    && $content->merchantOrderId == $request->order_id
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
                    throw new \Exception($content->statusMessage);
                }
            } else {
                throw new \Exception($content);
            }
        } catch (\Throwable $e) {
            throw new \Exception($this->ThrowError($e));
        }
        return $result ?? [];
    }
>>>>>>> Stashed changes

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

}
