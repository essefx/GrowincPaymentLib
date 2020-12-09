<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Xendit extends Requestor implements VendorInterface
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
		try{
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

			$this->form['payment_type'] = $this->transaction->getPaymentType();
			$this->form['payment_method'] = strtoupper($this->transaction->getPaymentMethod());
			$this->form['payment_url'] = $this->init->getPaymentURL() . '/callback_virtual_accounts';
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
			
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];
			
			 // Returns ISO8601 in proper format
			$expected_amount = gmdate("Y-m-d\TH:i:s\Z", strtotime("now") + ($this->form['expiry_period'] * 60));
			
			$this->request['data'] = [
				'external_id' => $this->form['order_id'],
				'bank_code' => $this->form['payment_method'],
				'name' => $this->form['customer_name'],
				"is_closed" => true,
				"expiration_date" => $expected_amount,
				"expected_amount" => $this->form['amount']
			];
			
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic '.base64_encode($this->init->getMID().':'),
				'Content-Length' => strlen(json_encode($this->request['data'])),
			];
			
			$this->request['option'] = [
				'as_json' => true,
			];
			
			// print_r($this->request['data']);exit();
			
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				// return print_r($content);
				if (	!empty($content->status)
						&& $content->status == 'PENDING'
				) {
					/* Success
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

					$content = [
							'status' => '000',
							'data' => (array) $content,
						];

					$result = [
							'request' => (array) $this->request,
							'response' => [
									'content' => json_encode($content),
									'status_code' => 200,
									'va_number' => $content['data']['account_number'],
									'bank_code' => $content['data']['bank_code'],
									'amount' => $content['data']['expected_amount'],
									'transaction_id' => $content['data']['owner_id'], // vendor transaction_id
									'order_id' => $content['data']['external_id'], // PGA order_id
									'payment_type' => $this->form['payment_type'],
									'transaction_status' => $content['data']['status'],
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
			if(strcmp($callbackToken, $this->init->getSecret()) === 0){
				$content = [
						'status' => '000',
						'data' => (array) $request,
					];
				$result = [
						'request' => (array) $request,
						'response' => [
								'content' => json_encode($content),
								'status_code' => 200,
								'order_id' => $content['data']['external_id'],
								'transaction_id' => $content['data']['payment_id'],
								'status' => '',
								'transaction_time' => $content['data']['transaction_timestamp'],
								'amount' => $content['data']['amount'],
								'bank_code' => $content['data']['bank_code'],
								'va_number' => $content['data']['account_number'],
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
			$content = [
					'status' => '000',
					'data' => (array) $request,
				];
			$result = [
					'request' => (array) $request,
					'response' => [
							'content' => json_encode($content),
							'status_code' => 200,
							'id' => $content['data']['id'],
							'order_id' => $content['data']['external_id'],
							'transaction_id' => $content['data']['owner_id'],
							'bank_code' => $content['data']['bank_code'],
							'va_number' => $content['data']['account_number'],
							'status' => $content['data']['status'],
							'expired_date' => $content['data']['expiration_date'],
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
			SELF::Validate($request, ['order_id','transaction_id']);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL() . '/callback_virtual_account_payments/payment_id=' . $request->transaction_id;
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic '.base64_encode($this->init->getMID().':')
			];

			$this->request['data'] = [];
			
			$get = $this->DoRequest('GET', $this->request);

			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if ( !isset($content->error_code)
						&& !isset($content->message)
				) {
					// Success
					/*
					{
						"id": "598d91b1191029596846047f",
						"payment_id": "1502450097080",
						"callback_virtual_account_id": "598d5f71bf64853820c49a18",
						"external_id": "demo-1502437214715",
						"merchant_code": "77517",
						"account_number": "1000016980",
						"bank_code": "BNI",
						"amount": 5000,
						"sender_name": "JOHN DOE",
						"transaction_timestamp": "2017-08-11T11:14:57.080Z"
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
									'va_number' => $content['data']['account_number'],
									'bank_code' => $content['data']['bank_code'],
									'amount' => $content['data']['amount'],
									'transaction_id' => $content['data']['payment_id'], // vendor transaction_id
									'order_id' => $content['data']['external_id'], // PGA order_id
									'transaction_status' => '',
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