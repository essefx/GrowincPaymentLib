<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Xendit extends Requestor implements VendorInterface
{

	protected $form;

	/*==========================================================================================
												Start of Private
	==========================================================================================**/

		public function _CreateCardToken($args)
		{
			// try {
			// 	$this->request['headers'] = [
			// 		'Content-Type' => 'application/json',
			// 		'Accept' => 'application/json',
			// 	];
			// 	$this->request['url'] = $args['token_url'];
			// 	$this->request['data'] = [
			// 			'amount' => $args['amount'],
			// 			'card_number' => $args['card_number'],
			// 			'card_exp_month' => $args['card_exp_month'],
			// 			'card_exp_year' => $args['card_exp_year'],
			// 			'card_cvn' => $args['card_cvn'],
			// 			'is_multiple_use' => $args['is_multiple_use'],
			// 			'should_authenticate' => $args['should_authenticate'],
			// 		];
			// 	$get = $this->DoRequest('GET', $this->request);
			// 	$response = (array) $get['response'];
			// 	extract($response);
			// 	if (!empty($status_code) && $status_code === 200) {
			// 		$content = (object) json_decode($content);
			// 		if (
			// 			!empty($content->status_code)
			// 			&& $content->status_code == 200
			// 		) {
			// 			$result = $content->token_id;
			// 		}
			// 	}
			// } catch (\Throwable $e) {
			// 	throw new \Exception($this->ThrowError($e));
			// }
			// return $result ?? [];
		}

	/*=================================   End of Private   ==================================*/

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
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['item'] = $this->transaction->getItem();
			$this->form['amount'] = (float) $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			// $this->form['customer_address'] = $this->transaction->getCustomerAddress();
			// $this->form['country_code'] = $this->transaction->getCountryCode();
			// //
			// $this->form['billing_address'] = [
			// 		'first_name' => $this->form['customer_name'],
			// 		'last_name' => '',
			// 		'email' => $this->form['customer_email'],
			// 		'phone' => $this->form['customer_phone'],
			// 		'address' => ' ',
			// 		'city' => ' ',
			// 		'postal_code' => ' ',
			// 		'country_code' => $this->form['country_code'],
			// 	];
			// $this->form['shipping_address'] = [
			// 		'first_name' => $this->form['customer_name'],
			// 		'last_name' => '',
			// 		'email' => $this->form['customer_email'],
			// 		'phone' => $this->form['customer_phone'],
			// 		'address' => ' ',
			// 		'city' => ' ',
			// 		'postal_code' => ' ',
			// 		'country_code' => $this->form['country_code'],
			// 	];
			// $this->form['customer_details'] = [
			// 		'first_name' => $this->form['customer_name'],
			// 		'last_name' => '',
			// 		'email' => $this->form['customer_email'],
			// 		'phone' => $this->form['customer_phone'],
			// 		'billing_address' => $this->form['billing_address'],
			// 		'shipping_address' => $this->form['shipping_address'],
			// 	];
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			//
			// $expire_date = gmdate("Y-m-d\TH:i:s\Z",  $this->transaction->getTime() + (3600 * ( 7 + $this->transaction->getExpireAt()))) . '+07:00';
			$expire_date = gmdate("Y-m-d\TH:i:s",  $this->transaction->getTime() + (3600 * ( 7 + $this->transaction->getExpireAt()))) . '+07:00';
			//
			switch ($payment_method) {
				case 'bank_transfer':
					$this->request['url'] = $this->init->getPaymentURL() . '/callback_virtual_accounts';
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'bank_code' => strtoupper($payment_channel),
							'name' => $this->form['customer_name'],
							'is_closed' => true, // When set to true, the virtual account will be closed and will only accept the amount specified in expected_amount
							'expected_amount' => $this->form['amount'],
							// 'expiration_date' => gmdate("Y-m-d\TH:i:s\Z", strtotime("now") + ($this->transaction->getExpireAt() * 60)),
							'expiration_date' => $expire_date,
							'is_single_use' => 'true',
							// 'description' => '', // This field is only supported for BRI
						];
					break;
				case 'credit_card':
					throw new \Exception("Currently Inapplicable", 1);
					break;
				case 'ewallet':
					$this->request['url'] = $this->init->getPaymentURL() . '/ewallets';
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'amount' => $this->form['amount'],
							'phone' => $this->form['customer_phone'],
							'ewallet_type' => strtoupper($payment_channel),
						];
					switch (strtoupper($payment_channel)) {
						case 'OVO':
							// Nothing as modification here
							break;
						case 'DANA':
							$this->request['data'] = array_merge($this->request['data'], [
									'callback_url' => $this->init->getCallbackURL(),
									'redirect_url' => $this->init->getReturnURL(),
								]);
							break;
						case 'LINKAJA':
							$this->request['data'] = array_merge($this->request['data'], [
									// 'items' => $this->transaction->getItem(),
									'items' => [
											[
													'id' => '1',
													'name' => $this->form['item'],
													'price' => $this->form['amount'],
													'quantity' => 1,
												],
										],
									'callback_url' => $this->init->getCallbackURL(),
									'redirect_url' => $this->init->getReturnURL(),
								]);
							break;
					}
					break;
				case 'qris':
					$this->request['url'] = $this->init->getPaymentURL() . '/qr_codes';
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'type' => 'DYNAMIC', // DYNAMIC QR code contains the payment value upon scanning and can be paid multiple times
							// 'type' => 'STATIC', // STATIC QR code requires end user to input the payment value and can be paid multiple times
							'callback_url' => $this->init->getCallbackURL(),
							'amount' => $this->form['amount'],
						];
					break;
				case 'cstore':
					$this->request['url'] = $this->init->getPaymentURL() . '/fixed_payment_code';
					$this->request['data'] = [
							'external_id' => $this->form['order_id'],
							'retail_outlet_name' => strtoupper($payment_channel),
							'name' => $this->form['customer_name'],
							'expected_amount' => $this->form['amount'],
							// 'payment_code' => '',
							// 'expiration_date' => gmdate("Y-m-d\TH:i:s\Z", strtotime("now") + ($this->transaction->getExpireAt() * 60)),
							'expiration_date' => $expire_date,
							'is_single_use' => 'true',
						];
					break;
			}

			if (!trim($this->request['url'])) {
				throw new \Exception("URL is empty", 1);
			}

			/*
			// $this->form['payment_type'] = $this->transaction->getPaymentType();
			// $this->form['payment_method'] = strtoupper($this->transaction->getPaymentMethod());
			$this->form['payment_url'] = $this->init->getPaymentURL() . '/callback_virtual_accounts';
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes

			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];

			// Returns ISO8601 in proper format
			$expiration_date = gmdate("Y-m-d\TH:i:s\Z", strtotime("now") + ($this->form['expiry_period'] * 60));

			$this->request['data'] = [
					'external_id' => $this->form['order_id'],
					'bank_code' => strtoupper($payment_channel),
					'name' => $this->form['customer_name'],
					"is_closed" => true,
					"expiration_date" => $expiration_date,
					"expected_amount" => $this->form['amount']
				];
			*/

			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = preg_replace('#/+#','/', $this->request['url']);
			$this->request['headers'] = [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
					'Content-Length' => strlen(json_encode($this->request['data'])),
				];
			$this->request['option'] = [
					'as_json' => true,
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				// return print_r($content);
				if (
					!empty($content->status)
					&&
						(
							$content->status == 'PENDING'
							|| $content->status == 'ACTIVE'
							|| $content->status == 'REQUEST_RECEIVED'
						)
				) {
					// Success
					/*
					{
						"is_closed": true,
						"status": "PENDING",
						"currency": "IDR",
						"owner_id": "5f706881fefc961e3f708f02",
						"external_id": "VA_fixed-1604335070",
						"bank_code": "BCA",
						"merchant_code": "10766",
						"name": "Lorem Ipsum",
						"account_number": "107669999610501",
						"expected_amount": 10000,
						"expiration_date": "2020-11-08T17:00:00.000Z",
						"is_single_use": false,
						"id": "5fa035de49715e400fc114f9"
					}
					*/
					// Pending
					/*
					{
						"id": "qr_a5ddd405-5ffb-4a63-a6f8-ca51c620bea1",
						"external_id": "0008177386",
						"amount": null,
						"qr_string": "00020101021126660014ID.LINKAJA.WWW011893600911002411480002152004230411480010303UME51450015ID.OR.GPNQR.WWW02150000000000000000303UME520454995802ID5920Placeholder merchant6007Jakarta610612345662190715fhBNl5rSWmgMfwV53033606304F35B",
						"callback_url": "https://ibank.growinc.dev/oanwef4851ashrb/pg/dk/redapi_result",
						"type": "STATIC",
						"status": "ACTIVE",
						"created": "2020-12-17T03:56:29.685Z",
						"updated": "2020-12-17T03:56:29.685Z"
					}
					*/
					$res = [
							'status' => '000',
							'data' => (array) $content,
						];
					$result = [
							'request' => (array) $this->request,
							'response' => [
									'content' => json_encode($res),
									'status_code' => 200,
									// 'va_number' => $content['data']['account_number'],
									// 'bank_code' => $payment_channel,
									// 'amount' => $content['data']['expected_amount'],
									// 'transaction_id' => $content['data']['owner_id'], // vendor transaction_id
									// 'order_id' => $content['data']['external_id'], // PGA order_id
									// 'payment_type' => $payment_method,
									// 'transaction_status' => $content['data']['status'],
								],
						];
				} else {
					throw new \Exception($content->status);
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
		// Example incoming data callback payment
		/*
		header x-callback-token
		{
			"amount": 50000,
			"callback_virtual_account_id": "5f8e658568ed5f402a1faadb",
			"payment_id": "5f8e658f8d65ab385241f17a",
			"external_id": "demo-va-callback",
			"account_number": "9999624535",
			"merchant_code": "88608",
			"bank_code": "MANDIRI",
			"transaction_timestamp": "2020-10-20T04:20:31.000Z",
			"currency": "IDR",
			"created": "2020-10-20T04:20:31.937Z",
			"updated": "2020-10-20T04:20:33.074Z",
			"id": "5f8e658fc5710a7ae00f75db",
			"owner_id": "5c2323c67d6d305ac433ba20"
		}
		*/
		try {
			$callbackToken = $_SERVER['HTTP_X_CALLBACK_TOKEN'];
			if (strcmp($callbackToken, $this->init->getSecret()) === 0) {
				$res = [
						'status' => '000',
						'data' => (array) $request,
					];
				$result = [
						'request' => (array) $request,
						'response' => [
								'content' => json_encode($res),
								'status_code' => 200,
								// 'order_id' => $content['data']['external_id'],
								// 'transaction_id' => $content['data']['payment_id'],
								// 'status' => '',
								// 'transaction_time' => $content['data']['transaction_timestamp'],
								// 'amount' => $content['data']['amount'],
								// 'bank_code' => $content['data']['bank_code'],
								// 'va_number' => $content['data']['account_number'],
							],
					];
			} else {
				throw new \Exception('Token check failed');
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function CallbackAlt(object $request)
	{
		// Example incoming data notification create VA
		/*
		{
			"id": "57fb4e076fa3fa296b7f5a97",
			"owner_id": "5824128aa6f9f9b648be9d76",
			"external_id": "va-1487156410",
			"merchant_code": "88608",
			"account_number": "886081000123456",
			"bank_code": "MANDIRI",
			"name": "John Doe",
			"is_closed": false,
			"is_single_use": false,
			"status": "ACTIVE",
			"expiration_date": "2048-02-15T11:01:52.722Z",
			"updated": "2016-10-10T08:15:03.404Z",
			"created": "2016-10-10T08:15:03.404Z"
		}
		*/
		try {
			$res = [
					'status' => '000',
					'data' => (array) $request,
				];
			$result = [
					'request' => (array) $request,
					'response' => [
							'content' => json_encode($res),
							'status_code' => 200,
							// 'id' => $content['data']['id'],
							// 'order_id' => $content['data']['external_id'],
							// 'transaction_id' => $content['data']['owner_id'],
							// 'bank_code' => $content['data']['bank_code'],
							// 'va_number' => $content['data']['account_number'],
							// 'status' => $content['data']['status'],
							// 'expired_date' => $content['data']['expiration_date'],
						],
				];
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, [
					'id'
				]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL() . '/callback_virtual_accounts/' . $request->id;
			$this->request['url'] = preg_replace('#/+#','/', $this->request['url']);
			$this->request['data'] = [];
			$this->request['headers'] = [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':')
				];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!isset($content->error_code)
					&& !isset($content->message)
				) {
					// Success
					/*
					{
						"status": "000",
						"data": {
							"is_closed": true,
							"status": "ACTIVE",
							"currency": "IDR",
							"owner_id": "5f706881fefc961e3f708f02",
							"external_id": "0008115320",
							"bank_code": "BCA",
							"merchant_code": "10766",
							"name": "LOREM IPSUM",
							"account_number": "107669999020779",
							"expected_amount": 100000,
							"expiration_date": "2020-12-16T12:22:00.000Z",
							"is_single_use": false,
							"id": "5fd9e479ed81dd402014403c"
						}
					}
					*/
					$res = [
							'status' => '000',
							'data' => (array) $content,
						];
					$result = [
							'request' => (array) $request,
							'response' => [
									'content' => json_encode($res),
									'status_code' => 200,
								],
						];
				} else {
					throw new \Exception($content->message);
				}
			} else {
				throw new \Exception($content);
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function InquiryPayment(object $request)
	{
		try {
			SELF::Validate($request, [
					'payment_id',
				]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL() . '/callback_virtual_account_payments/payment_id=' . $request->payment_id;
			$this->request['url'] = preg_replace('#/+#','/', $this->request['url']);
			$this->request['data'] = [];
			$this->request['headers'] = [
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':')
				];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!isset($content->error_code)
					&& !isset($content->message)
				) {
					// Success
					/*
					{
						"status": "000",
						"data": {
							"payment_id": "5f9fb9758d65ab3c1141f230",
							"callback_virtual_account_id": "5f9fb82dbcbf722b71041f3f",
							"external_id": "VA_fixed-1604302892",
							"account_number": "9999000002",
							"bank_code": "MANDIRI",
							"amount": 50000,
							"transaction_timestamp": "2020-11-02T07:47:01.000Z",
							"merchant_code": "88608",
							"currency": "IDR",
							"id": "5f9fb9758940c131b3d7b96d"
						}
					}
					*/
					$res = [
							'status' => '000',
							'data' => (array) $content,
						];
					$result = [
							'request' => (array) $request,
							'response' => [
									'content' => json_encode($res),
									'status_code' => 200,
									// 'va_number' => $content['data']['account_number'],
									// 'bank_code' => $content['data']['bank_code'],
									// 'amount' => $content['data']['amount'],
									// 'transaction_id' => $content['data']['payment_id'], // vendor transaction_id
									// 'order_id' => $content['data']['external_id'], // PGA order_id
									// 'transaction_status' => '',
								],
						];
				} else {
					throw new \Exception($content->message);
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
}
