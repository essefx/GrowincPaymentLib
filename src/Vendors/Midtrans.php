<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Midtrans extends Requestor implements VendorInterface
{

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		try {
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
			];
			$this->request['url'] = $args['token_url'];
			$this->request['data'] = [
					'client_key' => $args['client_key'],
					'card_number' => $args['card_number'],
					'card_exp_month' => $args['card_exp_month'],
					'card_exp_year' => $args['card_exp_year'],
					'card_cvv' => $args['card_cvv']
				];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status_code)
					&& $content->status_code == 200
				) {
					$result = $content->token_id;
				}
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
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
		// try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			// $this->form['amount'] = $this->transaction->getAmount(); // Inapplicable
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
					'last_name' => '',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => $this->form['customer_address'],
					'city' => '-',
					'postal_code' => '-',
					'country_code' => $this->form['country_code'],
				];
			$this->form['shipping_address'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => '',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => $this->form['customer_address'],
					'city' => '-',
					'postal_code' => '-',
					'country_code' => $this->form['country_code'],
				];
			$this->form['customer_details'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => '',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'billing_address' => $this->form['billing_address'],
					'shipping_address' => $this->form['shipping_address'],
				];
			// item details
			$this->form['item_details'] = $this->transaction->getItem();
			// amount
			$amount_total = 0;
			foreach ($this->form['item_details'] as $price) {
				$amount_total += (int) $price['price'] * (int) $price['quantity'];
			}
			/*
				bank transfer
					bank_transfer -> va -> permata, bni, bca, bri
					echannel (mandiri)
				internet banking (redirect url)
					bca_klikpay
					bca_klikbca (not activated)
					bri_epay (not activated)
					cimb_clicks
					danamon_online
				E-wallet
					qris (not activated)
					gopay
					shopeepay (not activated)
				telkomsel_cash (not activated)
				mandiri_ecash
				Over the Counter
					cstore
						indomaret
						alfamart
				akulaku
			*/
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = $arr[0] ?? '';
			$payment_channel = $arr[1] ?? '';
			//
			// $this->form['payment_method'] = $this->transaction->getPaymentMethod();
			// $this->form['payment_type'] = $this->transaction->getPaymentType();
			$this->form['payment_url'] = $this->init->getPaymentURL() . '/v2/charge';
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
			// go
			$this->form['data'] = [
				// 'payment_type' => $this->form['payment_type'],
				'payment_type' => $payment_method, // Default payment method/type is bank_transfer
				// $this->form['payment_type'] => [
				$payment_method => [
						// 'bank' => $this->form['payment_method'],
						'bank' => $payment_channel,
					],
				'transaction_details' => [
						'order_id' => $this->form['order_id'],
						'gross_amount' => (float) $amount_total,
					],
				'customer_details' => $this->form['customer_details'],
				'item_details' => $this->form['item_details']
			];
			// switch ($this->form['payment_type']) {
			switch ($payment_method) {
				case 'credit_card':
					$getToken = $this->GetToken([
							'time' => $this->transaction->getTime(),
							'token_url' => $this->init->getTokenURL(),
							'client_key' => $this->init->getSecret(),
							'card_number' => $this->transaction->getCardNumber(),
							'card_exp_month' => $this->transaction->getCardExpMonth(),
							'card_exp_year' => $this->transaction->getCardExpYear(),
							'card_cvv' => $this->transaction->getCardExpCvv()
						]);
					$this->form['data']['credit_card'] = [
							'token_id' => $getToken,
							'authentication' => true
						];
					break;
				case 'echannel': // echannel mandiri
					$this->form['data']['echannel'] = [
							'bill_info1' => 'Payment for ',
							'bill_info2' => $this->form['description'],
						];
					break;
				case 'bca_klikpay':
					$this->form['data']['bca_klikpay'] = [
							'type' => 1,
							'description' => $this->form['description'],
						];
					break;
				case 'bca_klikbca':
					$this->form['data']['bca_klikbca'] = [
							'user_id' => $this->transaction->getCustomerUserid(),
							'description' => $this->form['description'],
						];
					break;
				case 'bri_epay': case 'danamon_online': case 'akulaku':
					// unset($this->form['data'][$this->form['payment_type']]);
					unset($this->form['data'][$payment_method]);
					break;
				case 'cimb_clicks': case 'mandiri_ecash':
					// $this->form['data'][$this->form['payment_type']] = [
					$this->form['data'][$payment_method] = [
							'description' => $this->form['description'],
						];
					break;
				case 'gopay':
					$this->form['data']['gopay'] = [
							"enable_callback" => true,
							"callback_url" => $this->init->getCallbackURL(),
						];
					break;
				case 'qris':
					$this->form['data']['qris'] = [
							"acquirer" => "gopay"
						];
					break;
				case 'shopeepay':
					$this->form['data']['shopeepay'] = [
							"callback_url" => $this->init->getCallbackURL(),
						];
					break;
				case 'telkomsel_cash':
					$this->form['data']['telkomsel_cash'] = [
							"promo" => false,
							"is_reversal" => 0,
							"customer" => $this->form['customer_phone']
						];
					break;
				case 'cstore':
					// if ($this->form['payment_method'] == 'indomaret') {
					if ($payment_channel == 'indomaret') {
						// $this->form['data'][$this->form['payment_type']] = [
						$this->form['data'][$payment_method] = [
								// "store" => $this->form['payment_method'],
								"store" => $payment_channel,
								"message" => $this->form['description']
							];
					}
					// if ($this->form['payment_method'] == 'alfamart') {
					if ($payment_channel == 'alfamart') {
						// $this->form['data'][$this->form['payment_type']] = [
						$this->form['data'][$payment_method] = [
								// "store" => $this->form['payment_method'],
								"store" => $payment_channel,
								"alfamart_free_text_1" => 'Pembayaran',
								"alfamart_free_text_2" => $this->form['description'],
								"alfamart_free_text_3" => 'Terima kasih',
							];
					}
					break;
			}

			// credit_card token
			/*
			if ($this->form['payment_type'] == 'credit_card') {
				// credit card details
				$this->form['customer_credit_card'] = [
						'time' => $this->transaction->getTime(),
						'token_url' => $this->init->getTokenUrl(),
						'client_key' => $this->init->getSecret(),
						'card_number' => $this->transaction->getCardNumber(),
						'card_exp_month' => $this->transaction->getCardExpMonth(),
						'card_exp_year' => $this->transaction->gettCardExpYear(),
						'card_cvv' => $this->transaction->getCardExpCvv()
					];
				$getToken = $this->GetToken($this->form['customer_credit_card']);
				$this->form['cc_token'] = $getToken;
			}
			*/

			// echannel mandiri
			/*
			if ($this->form['payment_type'] == 'echannel') {
				// unset($this->request['data']['echannel']);
				$this->request['data']['echannel'] = [
						'bill_info1' => 'payment for:',
						'bill_info2' => $this->form['description'],
					];
			}
			*/

			// bca_klikpay
			/*
			if ($this->form['payment_type'] == 'bca_klikpay') {
				$this->request['data']['bca_klikpay'] = [
						'type' => 1,
						'description' => $this->form['description'],
					];
			}
			*/
			// bca_klikbca
			/*
			if ($this->form['payment_type'] == 'bca_klikbca') {
				$this->request['data']['bca_klikbca'] = [
						'user_id' => $this->transaction->getCustomerUserid(),
						'description' => $this->form['description'],
					];
			}
			*/
			// bri_epay , danamon_online , akulaku
			/*
			$arrDataBank = ['bri_epay', 'danamon_online', 'akulaku'];
			if (in_array($this->form['payment_type'], $arrDataBank)) {
				unset($this->request['data'][$this->form['payment_type']]);
			}
			*/
			// cimb_clicks , mandiri_ecash
			/*
			$arrDataBank2 = ['cimb_clicks', 'mandiri_ecash'];
			if (in_array($this->form['payment_type'], $arrDataBank2)) {
				$this->request['data'][$this->form['payment_type']] = [
						'description' => $this->form['description'],
					];
			}
			*/
			// gopay
			/*
			if ($this->form['payment_type'] == 'gopay') {
				$this->request['data']['gopay'] = [
						"enable_callback" => true,
						"callback_url" => "someapps://callback"
					];
			}
			*/
			// qris
			/*
			if ($this->form['payment_type'] == 'qris') {
				$this->request['data']['qris'] = [
						"acquirer" => "gopay"
					];
			}
			*/
			// shopeepay
			/*
			if ($this->form['payment_type'] == 'shopeepay') {
				$this->request['data']['shopeepay'] = [
						"callback_url" => "https://google.com/" // back url after success payment
					];
			}
			*/
			// telkomsel_cash
			/*
			if ($this->form['payment_type'] == 'telkomsel_cash') {
				$this->request['data']['telkomsel_cash'] = [
						"promo" => false,
						"is_reversal" => 0,
						"customer" => $this->form['customer_phone']
					];
			}
			*/
			// cstore
			/*
			if ($this->form['payment_type'] == 'cstore') {
				if ($this->form['payment_method'] == 'indomaret') {
					$this->request['data'][$this->form['payment_type']] = [
							"store" => $this->form['payment_method'],
							"message" => $this->form['description']
						];
				}
				if ($this->form['payment_method'] == 'alfamart') {
					$this->request['data'][$this->form['payment_type']] = [
							"store" => $this->form['payment_method'],
							"alfamart_free_text_1" => 'pembayaran',
							"alfamart_free_text_2" => $this->form['description'],
							"alfamart_free_text_3" => 'Terima kasih',
						];
				}
			}
			*/
			// credit_card
			/*
			if ($this->form['payment_type'] == 'credit_card') {
				$this->request['data']['credit_card'] = [
					'token_id' => $this->form['cc_token'],
					'authentication' => true
				];
				$this->request['data']['customer_details'] = $this->form['customer_details'];
			}
			*/
			// Go
			$this->request['data'] = $this->form['data'];
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];
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
// print_r($this->request['data']);
// print_r($post);
// exit();
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				// return print_r($content);
				if (
					!empty($content->status_code)
					&& $content->status_code == 201
				) {
					/* Success
					{
						"status_code": "201",
						"status_message": "Success, PERMATA VA transaction is successful",
						"transaction_id": "7ba0a676-24e5-4648-8b94-e2bf02888f8c",
						"order_id": "0003880223",
						"gross_amount": "100000.00",
						"currency": "IDR",
						"payment_type": "bank_transfer",
						"transaction_time": "2020-10-28 17:17:21",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"permata_va_number": "530002269464288",
						"merchant_id": "G345053042"
					}
					*/
					if(strtolower($this->form['payment_method']) == 'permata'){
						$va_number = $content->permata_va_number;
						$bank_code = strtolower($this->form['payment_method']);
					}else{
						$va_number = $content->va_numbers[0]->va_number;
						$bank_code = $content->va_numbers[0]->bank;
					}

					$content = [
						'status' => '000',
						'data' => (array) $content,
					];
					$result = [
							'request' => (array) $this->request,
							'response' => [
									'content' => json_encode($content),
									'status_code' => 200,
									'va_number' => $va_number,
									'bank_code' => $bank_code,
									'amount' => $content['data']['gross_amount'],
									'transaction_id' => $content['data']['transaction_id'], // vendor transaction_id
									'order_id' => $content['data']['order_id'], // PGA order_id
									'payment_type' => $content['data']['payment_type'],
									'transaction_status' => $content['data']['transaction_status'],
								],
						];
				} else {
					throw new \Exception($content->status_message);
				}
			} else {
				throw new \Exception($content);
			}
		// } catch (\Throwable $e) {
		// 	throw new \Exception($this->ThrowError($e));
		// }
		return $result ?? [];
	}

	public function Callback(object $request)
	{
		// Example incoming data
		/*
		{
			"status_code": "201",
			"status_message": "midtrans payment notification",
			"transaction_id": "6fd88567-62da-43ff-8fe6-5717e430ffc7",
			"order_id": "0003960969",
			"gross_amount": "150000.00",
			"payment_type": "bank_transfer",
			"transaction_time": "2016-06-19 18:23:21",
			"transaction_status": "settlement",
			"fraud_status": "accept",
			"permata_va_number": "8562000087926752",
			"signature_key": "b8d7baceab8967af2fdebb82f497fbf4be957e0147f34e910fe9abfc533f883f1206e6c7a72d111ff61331254e3ff9f609c16cc81762e15d9ee6c53de36c65ff"
		}
		*/
		try {
			SELF::Validate($request, [
					'order_id',
					'status_code',
					'gross_amount'
				]);
			$input = $request->order_id .
				$request->status_code .
				$request->gross_amount .
				$this->init->getMID();
			$signature = openssl_digest($input, 'sha512');
			// print_r($signature);
			// exit();
			if (strcmp($signature, $request->signature_key) === 0) {
				$content = [
					'status' => '000',
					'data' => (array) $request,
				];

				if(isset($content->permata_va_number)){
					$bank_code = 'Permata';
					$va_number = $content->permata_va_number;
				}else{
					$bank_code = $content->va_numbers[0]->bank;
					$va_number = $content->va_numbers[0]->va_number;
				}

				$result = [
					'request' => (array) $request,
					'response' => [
						'content' => json_encode($content),
						'status_code' => 200,
						'order_id' => $content['data']['order_id'],
						'transaction_id' => $content['data']['transaction_id'],
						'status' => $content['data']['transaction_status'],
						'transaction_time' => $content['data']['transaction_time'],
						'amount' => $content['data']['gross_amount'],
						'bank_code' => $bank_code,
						'va_number' => $va_number,
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

	public function CallbackAlt(object $request)
	{
		// Inapplicable
	}

	public function Inquiry(object $request)
	{
		try {
			SELF::Validate($request, ['order_id','transaction_id']);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL() . $request->order_id . '/status';
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':'),
			];

			$this->request['data'] = [];

			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status_code)
					&&
						( $content->status_code == "201" // pending
			 				|| $content->status_code == "200" // settlement
						)
					&& $content->order_id == $request->order_id
				) {
					// Success
					/*
					{
						"va_numbers": [
							{
								"bank": "bca",
								"va_number": "53042878140"
							}
						],
						"payment_amounts": [],
						"transaction_time": "2020-10-29 15:42:50",
						"gross_amount": "150000.00",
						"currency": "IDR",
						"order_id": "0003960969",
						"payment_type": "bank_transfer",
						"signature_key": "b8d7baceab8967af2fdebb82f497fbf4be957e0147f34e910fe9abfc533f883f1206e6c7a72d111ff61331254e3ff9f609c16cc81762e15d9ee6c53de36c65ff",
						"status_code": "201",
						"transaction_id": "e84972ee-b1e1-4f84-8fcd-a8a561999f08",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"status_message": "Success, transaction is found",
						"merchant_id": "G345053042"
					}
					*/
					if(strtolower($payment_channel) == 'permata'){
						$va_number = $content->permata_va_number;
						$bank_code = strtolower($payment_channel);
					}else{
						$va_number = $content->va_numbers[0]->va_number;
						$bank_code = $content->va_numbers[0]->bank;
					}

					$content = [
							'status' => '000',
							'data' => (array) $content,
						];
					$result = [
						'request' => (array) $request,
						'response' => [
							'content' => json_encode($content),
							'status_code' => 200,
							'va_number' => $va_number,
							'bank_code' => $bank_code,
							'amount' => $content['data']['gross_amount'],
							'transaction_id' => $content['data']['transaction_id'], // vendor transaction_id
							'order_id' => $content['data']['order_id'], // PGA order_id
							'transaction_status' => $content['data']['transaction_status'],
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
