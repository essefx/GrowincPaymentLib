<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Espay extends Requestor implements VendorInterface
{

	protected $form;

	/*==========================================================================================
												Start of Private
	==========================================================================================**/

		public function IncomingInquiry(object $request)
		{
			// Example incoming Inquiry payload
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
				SELF::Validate($request, [
					'rq_uuid',
					'rq_datetime',
						'order_id',
						'signature',
				]);
				/*------------------------------v Start of Section v---------- */
				//
				// Here check the va
				// Get the amount
				$amount = 100000;
				// Get the currency
				$currency = 'IDR';
				// Get the description
				$description = 'Payment';
				//
				/*------------------------------^ End of Section ^---------- */
				// Print result
				/* // Example format
					$result = "0;Success;$request->order_id;180000.00;IDR;Paymen For $request->order_id;$request->rq_datetime";
				*/
				$data =
					'0;' .
					'Success;' .
					$request->order_id . ';' .
					$amount . '.00;' .
					$currency . ';' .
					$description . ';' .
					$request->rq_datetime;
			} catch (\Throwable $e) {
				$data =
					'9;' .
					'Error;' .
					$request->order_id . ';' .
					$amount . '.00;' .
					$currency . ';' .
					$description . ';' .
					$request->rq_datetime;
			}
			return $data ?? [];
		}

		public function IncomingNotification(object $request)
		{
			// Example incoming Notify URL payload
			/*
			{
				"rq_uuid": "403e7d8b-5f57-417d-82ae-67ebc02bbbdd",
				"rq_datetime": "2021-06-25 15:15:15",
				"sender_id": "SGOPLUS",
				"receiver_id": "SGWGROWINC",
				"password": "Y0F,(5EM=#",
				"comm_code": "SGWGROWINC",
				"member_code": "4490587880694973",
				"member_cust_id": "SYSTEM",
				"member_cust_name": "SYSTEM",
				"ccy": "IDR",
				"amount": "100000",
				"debit_from": "4490587880694973",
				"debit_from_name": "4490587880694973",
				"debit_from_bank": "014",
				"credit_to": "1111111111111",
				"credit_to_name": "ESPAY AGGREGATOR",
				"credit_to_bank": "014",
				"payment_datetime": "2021-06-25 15:15:14",
				"payment_ref": "ESP1624608878QS4U",
				"payment_remark": "Payment",
				"order_id": "0024608873",
				"product_code": "BCAATM",
				"product_value": "4490587880694973",
				"message": "{\"CHANNEL_FLAG\":\"A\",\"PAY_AMOUNT_VA\":\"100000\"}",
				"status": "0",
				"token": "",
				"total_amount": "100000.00",
				"tx_key": "ESP1624608878QS4U",
				"fee_type": "S",
				"tx_fee": "0.00",
				"approval_code": "16246089143418135895",
				"member_id": "4490587880694973",
				"approval_code_full_bca": "4490587880694973",
				"signature": "faaf0dc3c372f9eff435d36b9cba0c6ce87891e90bb4aa0fb0b656b787f45fc5"
			}
			*/
			try {
				SELF::Validate($request, [
					'rq_uuid',
					'rq_datetime',
						'order_id',
						'signature',
				]);
				$params = $this->init->getParams();
				// To validate incoming payload
				$incoming_signature = strtoupper(
					'##' .
					$params['signature'] . // a. Signature Key(Key)
					'##' .
					$request->rq_datetime . // b. rq_datetime
					'##' .
					$request->order_id . // c. order_id
					'##' .
					'PAYMENTREPORT' . // d. PAYMENTREPORT-RS
					'##'
				);
				$incoming_signature_hashed = hash('sha256', $incoming_signature);
				// To send to Espay as response
				$response_signature = strtoupper(
					'##' .
					$params['signature'] . // a. Signature Key(Key)
					'##' .
					$request->rq_uuid . // b. rq_uuid
					'##' .
					$request->rq_datetime . // c. rq_datetime
					'##' .
					'0000' . // d. error_code
					'##' .
					'PAYMENTREPORT-RS' . // e. PAYMENTREPORT-RS
					'##'
				);
				$response_signature_hashed = hash('sha256', $response_signature);
				// Validate
				if (strcmp($request->signature, $incoming_signature_hashed) === 0) {
					// Success
					$data = [
						'rq_uuid' => $request->order_id,
						'rs_datetime' => $request->rq_datetime,
						'error_code' => '0000',
						'error_message' => 'Success',
						'signature' => $response_signature_hashed,
						'order_id' => $request->order_id,
						'reconcile_id' => 'REC' . $request->order_id, // Unique string / number as proof of acknowledgment / confirmation that partner has successfully received the notification
						'reconcile_datetime' => date("Y-m-d h:i:s"), // date("Y-M-D h:i:s"),
					];
				} else {
					throw new \Exception('Signature check failed');
				}
			} catch (\Throwable $e) {
				$data = [
					'rq_uuid' => $request->order_id,
					'rs_datetime' => $request->rq_datetime,
					'error_code' => '9999',
					'error_message' => 'Failed - ' . $e->getMessage(),
					'signature' => $response_signature_hashed,
					'order_id' => $request->order_id,
					'reconcile_id' => 'REC' . $request->order_id, // Unique string / number as proof of acknowledgment / confirmation that partner has successfully received the notification
					'reconcile_datetime' => date('Y-m-d h:i:s'),
				];
			}
			return $data ?? [];
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
			$params = $this->init->getParams();
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			$this->form['payment_channel'] = $arr;
			switch ($payment_method) {
				case 'va': case 'ibank': case 'cstore':
					$signature = strtoupper(
						'##' .
						$params['signature'] . // a. Signature Key(Key)
						'##' .
						$transaction->getInvoiceNo() . // b. rq_uuid
						'##' .
						date("Y-m-d H:i:s", $transaction->getTime()) . // c. rq_datetime
						'##' .
						$transaction->getOrderID() . // d. order_id
						'##' .
						$this->form['amount'] . // e. Amount
						'##' .
						$transaction->getCurrency() . // f. Ccy
						'##' .
						$this->init->getMID() . // g. comm_code
						'##' .
						'SENDINVOICE' . // h. Mode
						'##'
					);
					$signature_hashed = hash('sha256', $signature);
					$this->request['data'] = [
						'rq_uuid' => $this->form['invoice_no'],
						'rq_datetime' => date("Y-m-d H:i:s", $transaction->getTime()),
						'order_id' => $this->form['order_id'],
						'amount' => $this->form['amount'],
						'ccy' => $this->form['currency'],
						'comm_code' => $this->init->getMID(),
						'remark1' => $this->form['customer_phone'],
						'remark2' => $this->form['customer_name'],
						'remark3' => $this->form['customer_email'],
						'update' => 'N',
						'bank_code' => $payment_channel,
						'va_expired' => $this->transaction->getExpireAt() * 60,
						'password' => $params['password'],
						'signature' => $signature_hashed,
					];
					$this->request['url'] =
						SELF::CleanURL(
							$this->init->getPaymentURL() .
							'/rest/merchantpg/sendinvoice'
						);
					break;
				case 'ewallet':
					// OVO
					// JENIUS
					// GOPAY
					// LINKAJA
					$signature = strtoupper(
						'##' .
						$transaction->getInvoiceNo() . // a. rq_uuid
						'##' .
						$this->init->getMID() . // b. comm_code
						'##' .
						strtoupper($payment_channel) . // c. Product code
						'##' .
						$transaction->getOrderID() . // d. order_id
						'##' .
						$this->form['amount'] . // e. Amount
						'##' .
						'PUSHTOPAY' . // g. Mode
						'##' .
						$params['signature'] . // f. Signature Key(Key)
						'##'
					);
					$signature_hashed = hash('sha256', $signature);
					$this->request['data'] = [
						'rq_uuid' => $this->form['invoice_no'],
						'rq_datetime' => date("Y-m-d H:i:s", $transaction->getTime()),
						'comm_code' => $this->init->getMID(),
						'order_id' => $this->form['order_id'],
						'product_code' => strtoupper($payment_channel),
						'amount' => $this->form['amount'],
						'customer_id' => $this->form['customer_phone'],
						'promo_code' => '',
						'is_sync' => '0',
						'branch_id' => '',
						'pos_id' => '',
						'description' => $this->form['description'],
						'signature' => $signature_hashed,
					];
					$this->request['url'] =
						SELF::CleanURL(
							$this->init->getPaymentURL() .
							'/rest/digitalpay/pushtopay'
						);
					break;
				// case 'cstore':
				// 	break;
				// case 'ibank':
				// 	break;
				default:
					throw new \Exception("Payment method not defined", 1);
			}
			/*------------------------------v Start of Debug v---------- */
			$this->form['signature'] = $signature;
			$this->form['signature_hashed'] = $signature_hashed;
			//
			$this->form['authorization'] = $this->init->getMID() . ':' . $params['password'];
			// $this->form['authorization'] = 'GROWINC' . ':' . $params['password'];
			$this->form['authorization_encoded'] = base64_encode($this->form['authorization']);
			//
			$this->form['params'] = $params;
			/*------------------------------^ End of Debug ^---------- */
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['headers'] = [
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization' => 'Basic ' . base64_encode($this->form['authorization']),
				'Content-Length' => strlen(json_encode($this->request['data'])),
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			// print_r($this->request);
			// exit();
			$post = $this->DoRequest('POST', $this->request);
			// print_r($post);
			// exit();
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) &&
				$status_code === 200
			) {
				$content = (object) json_decode($content);
				if (
					isset($content->error_code)
					&& $content->error_code == '0000'
				) {
					/* // Success VA
					{
						"rq_uuid": "INV24010741",
						"rs_datetime": "2021-06-18 17:05:49",
						"error_code": "0000",
						"error_message": "",
						"va_number": "8920800847010889",
						"expired": "2021-06-18 19:05:48",
						"description": "Payment",
						"total_amount": "100000.00",
						"amount": "100000.00",
						"fee": "0.00",
						"bank_code": "008"
					}
					*/
					/* // Success EWALLET
					{
						"rq_uuid": "INV24533382",
						"rs_datetime": "2021-06-24 18:16:32",
						"error_code": "0000",
						"error_message": "Success",
						"trx_id": "ESP1624533389C5EE",
						"QRLink": "https:\/\/sandbox-api.espay.id\/rest\/digitalnotify\/qr\/?trx_id=ESP1624533389C5EE",
						"QRCode": "data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJMAAACTAQMAAACwK7lWAAAABlBMVEX\/\/\/8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAB+0lEQVRIieWWsa30IBCE1yIgww0g0QYZLdkNcHYDdktktGGJBuzMAfL+w\/NJ7w\/Z+CF0uvtOstjZ2cFEf22NzFvglSlqhy+PhBlSe+L15o3U4vFTwkLZtLpS24\/HP1JGkWz0NMnZynXk2k4hZaQ2cqd3V6r0W1sXa\/r58t3\/adrDsIbs1uROXc7f1nWxMfHi7efmi92m3SNhAx9zVlto+s25iBg0uzIvZMmrHa6RsTqn+kmKs+NEk4SN7fh2vI\/hdsCnhA23Nb4s4RhZMb8972XGu0WXKzUB1vuto5vpGuHTYCftlvD2rZdBthXHCage8h8iNqTaqk\/V6MP4GkXshkNphtcCPK4eCTMeHwWRsGnI956lm2mHIFk8r1mh50bCSKN09Lys6YgBppOwgPjBY9ye7OSPKGGQLVIz2kZ1ZhmDfmdAzzFefNK3b50MvQK+Ms2MIFGLiIWykIN+m3c72yhhQ7KwjKG2YyARw2Dt8ClVCgdy9BSxgDzAPYMTlTWXRcIouEfzdaNvdUhv\/nUz5FC2GM3PXT9sjYSNCTEAk7rmU+ZTwugbJ7AMxJMx3DMIsE9CQRV5YCSs3W9N\/jbTkd6rppuFNtA\/2dn2JGQtwzR8Wh5fScjg061djBaX1SNi7f3lGHKdMJ3fvvUy6Ldnd6Hh7fXnPUsv+1vrH6ThjE2DVkpUAAAAAElFTkSuQmCC"
					}
					*/
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception($content->error_message, 901);
				}
			} else {
				throw new \Exception($content, 902);
			}
		} catch (\Throwable $e) {
			return SELF::JSONError($e, 400);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
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
			SELF::Validate($request, [
				'order_id',
			]);
			$params = $this->init->getParams();
			$this->request['time'] = time();
			// To send to Espay as response
			$signature = strtoupper(
				'##' .
				$params['signature'] . // a. Signature Key(Key)
				'##' .
				date('Y-m-d H:i:s', $this->request['time']) . // c. rq_datetime
				'##' .
				$request->order_id . // e. order_id
				'##' .
				'CHECKSTATUS' . // f. CHECKSTATUS
				'##'
			);
			$signature_hashed = hash('sha256', $signature);
			// Go
			$this->request['data'] = [
				'uuid' => $this->request['time'],
				'rq_datetime' => date('Y-m-d H:i:s', $this->request['time']),
				'comm_code' => $this->init->getMID(),
				'order_id' => $request->order_id,
				'is_paymentnotif' => '',
				// is_paymentnotif if it is filled with:
				// Y = will hit Merchant's payment notif URL
				// N = will update trx_status to S in Espay Dashboard
				// Not sent/not filled/filled with "" = standard check payment status
				'signature' => $signature_hashed,
			];
			/*------------------------------v Start of Debug v---------- */
			$this->form['signature'] = $signature;
			$this->form['signature_hashed'] = $signature_hashed;
			//
			// $this->form['authorization'] = $this->init->getMID();
			// $this->form['authorization'] = $this->init->getMID() . ':' . $params['password'];
			// $this->form['authorization'] = 'GROWINC' . ':' . $params['password'];
			/*------------------------------^ End of Debug ^---------- */
			$this->request['url'] = SELF::CleanURL(
				$this->init->getRequestURL() .
				'/rest/merchant/status'
			);
			$this->request['form'] = $this->form;
			$this->request['headers'] = [
				// 'Content-Type' => 'application/x-www-form-urlencoded',
				// 'Authorization' => 'Basic ' . base64_encode($this->form['authorization']),
				// 'Content-Length' => strlen(json_encode($this->request['data'])),
			];
			$this->request['option'] = [
				// 'as_json' => true,
			];
			// print_r($this->request);
			// exit();
			$post = $this->DoRequest('POST', $this->request);
			// print_r($post);
			// exit();
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					isset($content->error_code)
					&& $content->error_code == '0000'
				) {
					// Success
					/*
					{
						"rq_uuid": "1624631633",
						"rs_datetime": "2021-06-25 21:33:55",
						"error_code": "0000",
						"error_message": "",
						"comm_code": "SGWGROWINC",
						"member_code": null,
						"tx_id": "ESP1608032198KBSU",
						"order_id": "0008032194",
						"ccy_id": "IDR",
						"amount": "180000",
						"refund_amount": 0,
						"tx_status": "IP",
						"tx_reason": "EXPIRED",
						"tx_date": "2020-12-15",
						"created": "2020-12-15 18:36:35",
						"expired": "2020-12-16 18:36:38",
						"bank_name": "BANK MANDIRI",
						"product_name": "Link Aja QR Pay",
						"product_value": "",
						"payment_ref": "",
						"merchant_code": "1002",
						"token": "",
						"member_cust_id": "SYSTEM",
						"member_cust_name": "SYSTEM",
						"debit_from_name": "",
						"debit_from_bank": "008",
						"credit_to": "1150000059313",
						"credit_to_name": "1150000059313",
						"credit_to_bank": "008",
						"payment_datetime": "2020-12-15 18:36:38"
					}
					*/
					// Payment status :
					// S = Success
					// F = Failed
					// SP = Suspect
					// IP = In Process
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception($content->error_message, 901);
				}
			} else {
				throw new \Exception($content, 902);
			}
		} catch (\Throwable $e) {
			return SELF::JSONError($e, 400);
		}
		return SELF::JSONResult($this->request, $res, $status_code);
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
	}

	public function Notification(object $request)
	{
	}

	public function CancelTransaction(\Growinc\Payment\Transaction $transaction)
	{
		// try {
		// 	$this->transaction = $transaction;
		// 	//
		// 	$credential = \explode("//", $this->transaction->getCredentialAttr());
		// 	$signature_key = $credential[0];
		// 	$credential_password = $credential[1];
		// 	$comm_code = $credential[2];
		// 	$expire_transaction = $credential[3];
		// 	//
		// 	$this->form['rq_uuid'] = $this->transaction->getRuuid();
		// 	$this->form['rq_datetime'] = $this->transaction->getReqDateTime();
		// 	$this->form['comm_code'] = $comm_code;
		// 	$this->form['order_id'] = $this->transaction->getOrderID();
		// 	$this->form['tx_remark'] = $this->transaction->getTransactionRemak();

		// 	$uppercase = strtoupper('##' . $signature_key . '##' .
		// 		$transaction->getReqDateTime() . '##' . $transaction->getOrderID() . '##' .
		// 		$expire_transaction . '##');
		// 	$signature = hash('sha256', $uppercase);

		// 	$this->form['signature'] = $signature;
		// 	//
		// 	$this->form['request_url'] = $this->init->getRequestURL();
		// 	// go
		// 	$this->request['form'] = $this->form;
		// 	$this->request['time'] = $this->transaction->getTime();
		// 	$this->request['url'] = $this->form['request_url'];

		// 	$this->request['data'] = [
		// 		'uuid' => $this->form['rq_uuid'],
		// 		'rq_datetime' => $this->form['rq_datetime'],
		// 		'comm_code' => $this->form['comm_code'],
		// 		'order_id' => $this->form['order_id'],
		// 		'tx_remark' => $this->form['tx_remark'],
		// 		'signature' => $this->form['signature'],
		// 	];

		// 	$this->request['headers'] = [
		// 		'Content-Type' => 'application/x-www-form-urlencoded',
		// 		// 'Content-Type' => 'application/json',
		// 		'Accept' => 'application/json',
		// 		'Authorization' => 'Basic ' . base64_encode($this->init->getMID()),
		// 		'Content-Length' => strlen(json_encode($this->request['data'])),
		// 	];

		// 	$this->request['option'] = [
		// 		'request_opt' => 'json',
		// 	];

		// 	$post = $this->DoRequest('POST', $this->request);

		// 	$response = (array) $post['response'];
		// 	extract($response);
		// 	$content = (object) json_decode($content);

		// 	if (!empty($status_code) && $status_code === 200) {
		// 		if (!empty($content->error_code) && ($content->error_code == 0000)) {

		// 			// "rq_uuid": "INV07937085",
		// 			// "rs_datetime": "2020-12-14 18:02:00",
		// 			// "error_code": "0000",
		// 			// "error_message": "",
		// 			// "tx_id": "ESP1607937091F0RA"

		// 			$content = [
		// 				'status' => '0000',
		// 				'data' => (array) $content,
		// 			];

		// 			$result = [
		// 				'request' => (array) $this->request,
		// 				'response' => [
		// 					'content' => json_encode($content),
		// 					'status_code' => 200,
		// 				],
		// 			];
		// 		} else {
		// 			throw new \Exception($content->error_message);
		// 		}
		// 	} else {
		// 		throw new \Exception($content);
		// 	}
		// } catch (\Throwable $e) {
		// 	throw new \Exception($this->ThrowError($e));
		// }
		// return $result ?? [];
	}

	// public function SecurePaymentWallet(\Growinc\Payment\Transaction $transaction)
	// {
	// 	try {
	// 		$this->transaction = $transaction;
	// 		// credential
	// 		$credential = \explode("//", $this->transaction->getCredentialAttr());
	// 		$signature_key = $credential[0];
	// 		$credential_password = $credential[1];
	// 		$comm_code = $credential[2];
	// 		$push_to_pay = $credential[3];
	// 		// payment method
	// 		$_paymentMethode =  explode(',', $this->transaction->getPaymentMethod());
	// 		$payment_method = $_paymentMethode[0] ?? '';
	// 		$payment_channel = $_paymentMethode[1] ?? '';
	// 		$this->form['payment_type'] = $this->getPayId($_paymentMethode);
	// 		$product_name_payment = $this->getPayId($_paymentMethode)->name;
	// 		// item details
	// 		$this->form['item_details'] = $this->transaction->getItem();
	// 		$amount_total = 0;
	// 		foreach ($this->form['item_details'] as $price) {
	// 			$amount_total += (int) $price['price'] * (int) $price['quantity'];
	// 		}
	// 		// signature
	// 		$uppercase = strtoupper('##' . $transaction->getInvoiceNo() . '##' . $comm_code . '##' .  $this->form['payment_type']->id
	// 			. '##' .  $transaction->getOrderID() . '##' . $amount_total . '##' . $push_to_pay . '##' . $signature_key . '##');
	// 		$signature = hash('sha256', $uppercase);
	// 		//
	// 		$this->form['customer_name'] = $this->transaction->getCustomerName();
	// 		$this->form['customer_email'] = $this->transaction->getCustomerEmail();
	// 		$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
	// 		$this->form['country_code'] = $this->transaction->getCountrycode();

	// 		$this->form['billing_address'] = [
	// 			'first_name' => $this->form['customer_name'],
	// 			'last_name' => 'IPSUM',
	// 			'email' => $this->form['customer_email'],
	// 			'phone' => $this->form['customer_phone'],
	// 			'address' => 'sudirman',
	// 			'city' => 'Jakarta',
	// 			'postal_code' => '12190',
	// 			'country_code' => $this->form['country_code'],
	// 		];
	// 		$this->form['shipping_address'] = [
	// 			'first_name' => $this->form['customer_name'],
	// 			'last_name' => 'IPSUM',
	// 			'email' => $this->form['customer_email'],
	// 			'phone' => $this->form['customer_phone'],
	// 			'address' => 'sudirman',
	// 			'city' => 'Jakarta',
	// 			'postal_code' => '12190',
	// 			'country_code' => $this->form['country_code'],
	// 		];
	// 		$this->form['customer_details'] = [
	// 			'first_name' => $this->form['customer_name'],
	// 			'last_name' => 'IPSUM',
	// 			'email' => $this->form['customer_email'],
	// 			'phone' => $this->form['customer_phone'],
	// 			'billing_address' => $this->form['billing_address'],
	// 			'shipping_address' => $this->form['shipping_address'],
	// 		];
	// 		//
	// 		// $this->form['product_code'] = $this->transaction->getProductCode();
	// 		$this->form['rq_uuid'] = $this->transaction->getInvoiceNo();
	// 		$this->form['rq_datetime'] = date('Y-m-d H:i:s', $this->transaction->getTime()); // $this->transaction->getTime();
	// 		$this->form['comm_code'] = $comm_code;
	// 		$this->form['order_id'] = $this->transaction->getOrderID();
	// 		$this->form['customer_id'] = $this->transaction->getCustomerUserid();
	// 		$this->form['promo_code'] = ''; // $this->transaction->getPromoCode();
	// 		$this->form['is_sync'] = $this->transaction->getIsAsync();
	// 		$this->form['branch_id'] = $this->transaction->getBranchId();
	// 		$this->form['pos_id'] = $this->transaction->getPostId();
	// 		$this->form['description'] = $this->transaction->getDescription();
	// 		$this->form['amount'] = (float) $amount_total;
	// 		$this->form['signature'] = $signature;
	// 		$this->form['signature_raw'] = $uppercase;

	// 		$this->form['payment_url'] = $this->init->getPaymentURL();
	// 		// go
	// 		$this->request['form'] = $this->form;
	// 		$this->request['time'] = $this->transaction->getTime();
	// 		$this->request['url'] = $this->form['payment_url'];

	// 		$this->request['data'] = [
	// 			'rq_uuid' => $this->form['rq_uuid'],
	// 			'rq_datetime' => $this->form['rq_datetime'],
	// 			'comm_code' => $this->form['comm_code'],
	// 			'order_id' => $this->form['order_id'],
	// 			'product_code' => $this->form['payment_type']->id,
	// 			'amount' => $this->form['amount'],
	// 			'customer_id' => $this->form['customer_id'],
	// 			'promo_code' => $this->form['promo_code'],
	// 			'is_sync' => $this->form['is_sync'],
	// 			'branch_id' => $this->form['branch_id'],
	// 			'pos_id' => $this->form['pos_id'],
	// 			'description' => $this->form['description'],
	// 			'signature' => $this->form['signature'],
	// 		];

	// 		$this->request['headers'] = [
	// 			'Content-Type' => 'application/x-www-form-urlencoded',
	// 			'Accept' => 'application/json',
	// 			'Authorization' => 'Basic ' . base64_encode("GROWINC:$credential_password"),
	// 			'Content-Length' => strlen(json_encode($this->request['data'])),
	// 		];

	// 		$this->request['option'] = [
	// 			'request_opt' => 'json',
	// 		];

	// 		$post = $this->DoRequest('POST', $this->request);

	// 		$response = (array) $post['response'];

	// 		\extract($response);

	// 		if (!empty($status_code) && $status_code === 200) {
	// 			$content = (object) \json_decode($content);

	// 			if (!empty($content->error_code) && $content->error_code !== 0000) {
	// 				// OVO
	// 				// "rq_uuid": "INV08029063",
	// 				// "rs_datetime": "2020-12-15 17:44:32",
	// 				// "error_code": "0000",
	// 				// "error_message": "",
	// 				// "trx_id": "ESP16080290677RK8",
	// 				// "customer_id": "081111504410",
	// 				// "order_id": "0008029063",
	// 				// "trx_status": "SP",
	// 				// "amount": "180000",
	// 				// "approval_code": "110163",
	// 				// "product_code": "OVO"

	// 				// LINK
	// 				// "rq_uuid": "INV08030123",
	// 				// "rs_datetime": "2020-12-15 18:02:10",
	// 				// "error_code": "0000",
	// 				// "error_message": "Success",
	// 				// "trx_id": "ESP1608030129DDNZ",
	// 				// "QRLink": "https://sandbox-api.espay.id/rest/digitalnotify/qr/?trx_id=ESP1608030129DDNZ",
	// 				// "QRCode": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJMAAACTAQMAAACwK7lWAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAB+0lEQVRIieWWsa30IBCE1yIgww0g0QYZLdkNcHYDdktktGGJBuzMAfL+w/NJ7w/Z+CF0uvtOstjZ2cFEf22NzFvglSlqhy+PhBlSe+L15o3U4vFTwkLZtLpS24/HP1JGkWz0NMnZynXk2k4hZaQ2cqd3V6r0W1sXa/r58t3/adrDsIbs1uROXc7f1nWxMfHi7efmi92m3SNhAx9zVlto+s25iBg0uzIvZMmrHa6RsTqn+kmKs+NEk4SN7fh2vI/hdsCnhA23Nb4s4RhZMb8972XGu0WXKzUB1vuto5vpGuHTYCftlvD2rZdBthXHCage8h8iNqTaqk/V6MP4GkXshkNphtcCPK4eCTMeHwWRsGnI956lm2mHIFk8r1mh50bCSKN09Lys6YgBppOwgPjBY9ye7OSPKGGQLVIz2kZ1ZhmDfmdAzzFefNK3b50MvQK+Ms2MIFGLiIWykIN+m3c72yhhQ7KwjKG2YyARw2Dt8ClVCgdy9BSxgDzAPYMTlTWXRcIouEfzdaNvdUhv/nUz5FC2GM3PXT9sjYSNCTEAk7rmU+ZTwugbJ7AMxJMx3DMIsE9CQRV5YCSs3W9N/jbTkd6rppuFNtA/2dn2JGQtwzR8Wh5fScjg061djBaX1SNi7f3lGHKdMJ3fvvUy6Ldnd6Hh7fXnPUsv+1vrH6ThjE2DVkpUAAAAAElFTkSuQmCC"

	// 				$content = [
	// 					'status' => '0000',
	// 					'data' => (array) $content,
	// 				];

	// 				if ($payment_channel === 'ovo') {
	// 					$__tx_status = ['SP' => 'Suspect', 'IP' => 'In Process', 'F' => 'Failed', 'S' => 'Success'];
	// 					$status = $__tx_status[$content['data']['trx_status']] ?? $content['data']['trx_status'];
	// 				}

	// 				switch ($payment_channel) {
	// 					case 'ovo':
	// 						$result = [
	// 							'request' => (array) $this->request,
	// 							'response' => [
	// 								'content' => json_encode($content),
	// 								'status_code' => 200,
	// 								'transaction_id' => $content['data']['trx_id'],
	// 								'order_id' => $content['data']['order_id'], // PGA order_id
	// 								'payment_type' => $payment_method,
	// 								'product_name_payment' => $content['data']['product_code'],
	// 								'amount' => $content['data']['amount'],
	// 								'transaction_status' => $status,
	// 							]
	// 						];
	// 						break;
	// 					case 'link_aja':
	// 						$result = [
	// 							'request' => (array) $this->request,
	// 							'response' => [
	// 								'content' => \json_encode($content),
	// 								'status_code' => 200,
	// 								'transaction_id' => $content['data']['trx_id'],
	// 								'quick_response_link' => (string) $content['data']['QRLink'],
	// 								'quick_response_code' => (string) $content['data']['QRCode'],
	// 								'order_id' => $this->request['data']['order_id'], // PGA order_id
	// 								'payment_type' => $payment_method,
	// 								'product_name_payment' => $product_name_payment,
	// 								'amount' => $amount_total,
	// 								'transaction_status' => 'In Progres',
	// 							]
	// 						];
	// 				}
	// 			} else {
	// 				throw new \Exception($content->error_message);
	// 			}
	// 		} else {
	// 			throw new \Exception($content);
	// 		}
	// 	} catch (\Throwable $e) {
	// 		throw new \Exception($this->ThrowError($e));
	// 	}
	// 	return $result ?? [];
	// }

	public function CancelTransactionWallet(\Growinc\Payment\Transaction $transaction)
	{
		// try {
		// 	$this->transaction = $transaction;
		// 	// credential
		// 	$credential = \explode("//", $this->transaction->getCredentialAttr());
		// 	$signature_key = $credential[0];
		// 	$credential_password = $credential[1];
		// 	$comm_code = $credential[2];
		// 	$_void = $credential[3];
		// 	// signature
		// 	$uppercase = strtoupper('##' . $transaction->getRuuid() . '##' . $comm_code . '##' .  $transaction->getProductCode()
		// 		. '##' .  $transaction->getOrderID() . '##' . $transaction->getAmount() . '##' . $_void . '##' . $signature_key . '##');
		// 	$signature = hash('sha256', $uppercase);
		// 	//
		// 	$this->form['rq_uuid'] = $this->transaction->getRuuid();
		// 	$this->form['rq_datetime'] = $this->transaction->getReqDateTime();
		// 	$this->form['comm_code'] = $comm_code;
		// 	$this->form['order_id'] = $this->transaction->getOrderID();
		// 	$this->form['trx_id'] = $this->transaction->getTransactionID();
		// 	$this->form['product_code'] = $this->transaction->getProductCode();
		// 	$this->form['amount'] = $this->transaction->getAmount();
		// 	$this->form['signature'] = $signature;
		// 	//
		// 	$this->form['request_url'] = $this->init->getRequestURL();
		// 	// go
		// 	$this->request['form'] = $this->form;
		// 	$this->request['time'] = $this->transaction->getTime();
		// 	$this->request['url'] = $this->form['request_url'];

		// 	$this->request['data'] = [
		// 		'rq_uuid' => $this->form['rq_uuid'],
		// 		'rq_datetime' => $this->form['rq_datetime'],
		// 		'comm_code' => $this->form['comm_code'],
		// 		'order_id' => $this->form['order_id'],
		// 		'trx_id' => $this->form['trx_id'],
		// 		'product_code' => $this->form['product_code'],
		// 		'amount' => $this->form['amount'],
		// 		'signature' => $this->form['signature'],
		// 	];

		// 	$this->request['headers'] = [
		// 		'Content-Type' => 'application/x-www-form-urlencoded',
		// 		// 'Content-Type' => 'application/json',
		// 		'Accept' => 'application/json',
		// 		'Authorization' => 'Basic ' . base64_encode("GROWINC:$credential_password"),
		// 		'Content-Length' => strlen(json_encode($this->request['data'])),
		// 	];

		// 	$this->request['option'] = [
		// 		'request_opt' => 'json',
		// 	];

		// 	$post = $this->DoRequest('POST', $this->request);

		// 	$response = (array) $post['response'];
		// 	extract($response);
		// 	$content = (object) json_decode($content);

		// 	if (!empty($status_code) && $status_code === 200) {
		// 		if (!empty($content->error_code) && ($content->error_code == 0000)) {

		// 			// "rq_uuid": "INV08101194",
		// 			// "rs_datetime": "2020-12-16 14:28:48",
		// 			// "error_code": "0000",
		// 			// "error_message": "",
		// 			// "order_id": "0008101194",
		// 			// "trx_id": "ESP1608101205KMY7",
		// 			// "trx_status": "V"

		// 			$content = [
		// 				'status' => '0000',
		// 				'data' => (array) $content,
		// 			];

		// 			$result = [
		// 				'request' => (array) $this->request,
		// 				'response' => [
		// 					'content' => json_encode($content),
		// 					'status_code' => 200,
		// 				],
		// 			];
		// 		} else {
		// 			throw new \Exception($content->error_desc);
		// 		}
		// 	} else {
		// 		throw new \Exception($content);
		// 	}
		// } catch (\Throwable $e) {
		// 	throw new \Exception($this->ThrowError($e));
		// }
		// return $result ?? [];
	}

	// //
	// public function getPayId($paymentId)
	// {
	// 	switch ($paymentId[0]) {
	// 			/* Bank Transfer */
	// 		case '':
	// 			switch ($paymentId[1]) {
	// 				case 'bca':
	// 					$id = '014';
	// 					$name = 'BCAATM';
	// 					break;
	// 				case 'bri':
	// 					$id = '002';
	// 					$name = 'BRIATM';
	// 					break;
	// 				case 'cimb':
	// 					$id = '022';
	// 					$name = 'CIMBATM';
	// 					break;
	// 				case 'danamon':
	// 					$id = '011';
	// 					$name = 'DANAMONATM';
	// 					break;
	// 				case 'mandiri':
	// 					$id = '008';
	// 					$name = 'MANDIRIATM';
	// 					break;
	// 				case 'maybank':
	// 					$id = '016';
	// 					$name = 'MAYBANK';
	// 					break;
	// 				case 'permata':
	// 					$id = '013';
	// 					$name = 'PERMATAATM';
	// 					break;
	// 				default:
	// 					$id = '014';
	// 					$name = 'BCAATM';
	// 					break;
	// 					// case 'bni':
	// 					//     $id = '009';
	// 					//     $name = 'BNIATM';
	// 					// break;
	// 					// case 'bptn':
	// 					//     $id = '075';
	// 					//     $name = 'BPTN';
	// 					// break;
	// 					// case 'btpn':
	// 					//     $id = '213';
	// 					//     $name = 'BTPNWOW';
	// 					// break;
	// 					// case 'mandiri_syariah':
	// 					//     $id = '451';
	// 					//     $name = 'MANDIRISYARIAHATM';
	// 					// break;
	// 					// case 'maspion':
	// 					//     $id = '157';
	// 					//     $name = 'MASPIONATM';
	// 					// break;
	// 			}
	// 			break;

	// 			/* Bank Transfer */
	// 		case 'internet_banking':
	// 			switch ($paymentId[1]) {
	// 				case 'bca':
	// 					$id = '014';
	// 					$name = 'BCA VA Online';
	// 					break;
	// 				case 'cimb':
	// 					$id = '022';
	// 					$name = 'VA CIMB Niaga';
	// 					break;
	// 				case 'danamon':
	// 					$id = '011';
	// 					$name = 'Danamon Online Banking';
	// 					break;
	// 				case 'dbs':
	// 					$id = '046';
	// 					$name = 'DBS VA';
	// 					break;
	// 				case 'mandiri':
	// 					$id = '008';
	// 					$name = 'MANDIRI VA';
	// 					break;
	// 				case 'maybank':
	// 					$id = '016';
	// 					$name = 'MAYBANK va';
	// 					break;
	// 				case 'permata':
	// 					$id = '013';
	// 					$name = 'PERMATA VA';
	// 					break;
	// 				default:
	// 					$id = '014';
	// 					$name = 'BCA VA Online';
	// 					break;
	// 			}
	// 			break;


	// 			// case 'credit_card':
	// 			// 	switch ($paymentId[3]) {
	// 			// 		case 'ccinstall3':
	// 			// 			$id = '008';
	// 			// 			$name = 'CCINSTALL3';
	// 			// 		break;
	// 			// 		case 'ccinstal12':
	// 			// 			$id = '008';
	// 			// 			$name = 'CCINSTALL12';
	// 			// 		break;
	// 			// 		case 'visa_master':
	// 			// 			$id = '008';
	// 			// 			$name = 'Credit Card Visa / Master';
	// 			//         break;
	// 			//         case 'ccv_promotion':
	// 			// 			$id = '008';
	// 			// 			$name = 'CCPROMO';
	// 			// 		break;
	// 			// 		case 'ccinstall6':
	// 			// 			$id = '008';
	// 			// 			$name = 'CCINSTALL6';
	// 			// 		break;
	// 			// 	}
	// 			// break;
	// 	}
	// 	return (object) ['id' => $id, 'name' => $name];
	// }
}
