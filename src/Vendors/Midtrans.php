<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Midtrans extends Requestor implements VendorInterface
{

	protected $form;

	/*==========================================================================================
												Start of Private
	==========================================================================================**/

		public function _CreateCardToken($args)
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
			$this->form['shipping_address'] = $this->form['billing_address'];
			$this->form['customer_details'] = [
				'first_name' => $this->form['customer_name'],
				'last_name' => '',
				'email' => $this->form['customer_email'],
				'phone' => $this->form['customer_phone'],
				'billing_address' => $this->form['billing_address'],
				'shipping_address' => $this->form['shipping_address'],
			];
			//
			$this->form['item_details'] = [
				[
					'id' => '1',
					'price' => $this->form['amount'],
					'quantity' => 1,
					'name' => $this->form['item'],
					'brand' => '',
					'category' => '',
					'merchant_name' => '',
				],
			];
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
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			//
			$this->form['payment_url'] = SELF::CleanURL(
				$this->init->getPaymentURL() .
				'/v2/charge'
			);
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
			// Default payment method
			$this->form['data'] = [
				'payment_type' => $payment_method, // Default payment method/type is bank_transfer
				$payment_method => [
					'bank' => $payment_channel,
				],
				'transaction_details' => [
						'order_id' => $this->form['order_id'],
						'gross_amount' => (float) $this->form['amount'],
					],
				'customer_details' => $this->form['customer_details'],
				'item_details' => $this->form['item_details']
			];
			switch ($payment_method) {
				case 'credit_card':
					$card_token = $this->_CreateCardToken([
							'time' => $this->transaction->getTime(),
							'token_url' => $this->init->getTokenURL(),
							'client_key' => $this->init->getMID(),
							'card_number' => $this->transaction->getCardNumber(),
							'card_exp_month' => $this->transaction->getCardExpMonth(),
							'card_exp_year' => $this->transaction->getCardExpYear(),
							'card_cvv' => $this->transaction->getCardCVV()
						]);
					$this->form['data']['credit_card'] = [
							'token_id' => $card_token,
							'authentication' => true
						];
					break;
				// case 'echannel': // echannel mandiri
				// 	$this->form['data']['echannel'] = [
				// 			'bill_info1' => 'Payment for ',
				// 			'bill_info2' => $this->form['description'],
				// 		];
				// 	break;
				// case 'bca_klikpay':
				// 	$this->form['data']['bca_klikpay'] = [
				// 			'type' => 1,
				// 			'description' => $this->form['description'],
				// 		];
				// 	break;
				// case 'bca_klikbca':
				// 	$this->form['data']['bca_klikbca'] = [
				// 			'user_id' => $this->transaction->getCustomerUserid(),
				// 			'description' => $this->form['description'],
				// 		];
				// 	break;
				// case 'bri_epay': case 'danamon_online': case 'akulaku':
				// 	// unset($this->form['data'][$this->form['payment_type']]);
				// 	unset($this->form['data'][$payment_method]);
				// 	break;
				// case 'cimb_clicks': case 'mandiri_ecash':
				// 	// $this->form['data'][$this->form['payment_type']] = [
				// 	$this->form['data'][$payment_method] = [
				// 			'description' => $this->form['description'],
				// 		];
				// 	break;
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
				// case 'shopeepay':
				// 	$this->form['data']['shopeepay'] = [
				// 			"callback_url" => $this->init->getCallbackURL(),
				// 		];
				// 	break;
				// case 'telkomsel_cash':
				// 	$this->form['data']['telkomsel_cash'] = [
				// 			"promo" => false,
				// 			"is_reversal" => 0,
				// 			"customer" => $this->form['customer_phone']
				// 		];
				// 	break;
				case 'cstore':
					if ($payment_channel == 'indomaret') {
						$this->form['data'][$payment_method] = [
							"store" => $payment_channel,
							"message" => $this->form['description']
						];
					}
					if ($payment_channel == 'alfamart') {
						$this->form['data'][$payment_method] = [
							"store" => $payment_channel,
							"alfamart_free_text_1" => 'Pembayaran',
							"alfamart_free_text_2" => $this->form['description'],
							"alfamart_free_text_3" => 'Terima kasih',
						];
					}
					break;
			}
			// Go
			$this->request['data'] = $this->form['data'];
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getSecret() . ':'),
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
				if (
					!empty($content->status_code)
					&& $content->status_code == 201
				) {
					/* // Success VA BCA
					{
						"status_code": "201",
						"status_message": "Success, Bank Transfer transaction is created",
						"transaction_id": "c5b2e8cb-bb78-484d-a73f-b73eaa7b6e1b",
						"order_id": "0025325335",
						"merchant_id": "G072317714",
						"gross_amount": "100000.00",
						"currency": "IDR",
						"payment_type": "bank_transfer",
						"transaction_time": "2021-07-03 22:15:34",
						"transaction_status": "pending",
						"va_numbers": [{
							"bank": "bca",
							"va_number": "17714059768"
						}],
						"fraud_status": "accept"
					}
					*/
					/* // Success VA Permata
					{
						"status_code": "201",
						"status_message": "Success, PERMATA VA transaction is successful",
						"transaction_id": "2519f174-feea-4408-989a-713a62b6b9d4",
						"order_id": "0025325392",
						"gross_amount": "100000.00",
						"currency": "IDR",
						"payment_type": "bank_transfer",
						"transaction_time": "2021-07-03 22:16:31",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"permata_va_number": "177009624038289",
						"merchant_id": "G072317714"
					}
					*/
					/* // Success QRIS
					{
						"status_code": "201",
						"status_message": "QRIS transaction is created",
						"transaction_id": "4ab09c7e-7cdb-4791-b02f-c4749afde6d2",
						"order_id": "0025325654",
						"merchant_id": "G072317714",
						"gross_amount": "100000.00",
						"currency": "IDR",
						"payment_type": "qris",
						"transaction_time": "2021-07-03 22:20:53",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"actions": [{
							"name": "generate-qr-code",
							"method": "GET",
							"url": "https:\/\/api.sandbox.veritrans.co.id\/v2\/qris\/4ab09c7e-7cdb-4791-b02f-c4749afde6d2\/qr-code"
						}],
						"qr_string": "00020101021226620014COM.GO-JEK.WWW011993600914307231771410210G0723177140303UKE51440014ID.CO.QRIS.WWW0215AID2975932007870303UKE5204302453033605802ID5906VOGame6015JAKARTA SELATAN6105123205409100000.0062475036a150227e-50c4-4b82-8b9b-5532d5df934e0703A0163041006",
						"acquirer": "gopay"
					}
					*/
					/* // Success Alfamart
					{
						"status_code": "201",
						"status_message": "Success, cstore transaction is successful",
						"transaction_id": "992154fc-66ff-49ea-800d-38c4681e8d3f",
						"order_id": "0025325773",
						"merchant_id": "G072317714",
						"gross_amount": "100000.00",
						"currency": "IDR",
						"payment_type": "cstore",
						"transaction_time": "2021-07-03 22:22:53",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"payment_code": "7231279656124571",
						"store": "alfamart"
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					$res = [
						'status' => $content->status_message ?? 999,
						'data' => (array) $content,
					];
				}
				$result = [
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
				$this->init->getSecret();
			$signature = openssl_digest($input, 'sha512');
			if (strcmp($signature, $request->signature_key) === 0) {
				$res = [
					'status' => '000',
					'data' => (array) $request,
				];
				$result = [
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
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				$request->order_id . '/status'
			);
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode($this->init->getSecret() . ':'),
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
					/* // Success Alfamart
					{
						"payment_code": "7231279656124571",
						"store": "alfamart",
						"transaction_time": "2021-07-03 22:22:53",
						"gross_amount": "100000.00",
						"currency": "IDR",
						"order_id": "0025325773",
						"payment_type": "cstore",
						"signature_key": "1f3b7204827c0d49a25f46d9a09c4d4c00a1995c385a5577913396ff471ec28f35f9a26cd01b7943dbb34a89b31d4243854581d601d85dd9f18683c6f9953739",
						"status_code": "201",
						"transaction_id": "992154fc-66ff-49ea-800d-38c4681e8d3f",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"status_message": "Success, transaction is found",
						"merchant_id": "G072317714"
					}
					*/
					/* // Success VA BCA
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
