<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;
use Growinc\Payment\Transaction;

class Winpay extends Requestor implements VendorInterface
{

	protected $form;

	/*==========================================================================================
												Start of Private functions
	==========================================================================================**/

		private function _OpenSSLEncrypt($message, $key)
		{
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$secret_key = $key;
			$secret_iv = $key;
			$key = hash('sha256', $secret_key);
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
			$output = openssl_encrypt($message, $encrypt_method, $key, 0, $iv);
			$output = trim(base64_encode($output));
			return $output;
		}

		/*
		private function _getPaymentName($payment_channel_arr)
		{
			switch (strtolower($payment_channel_arr[0])) {
				case 'bank_transfer':
					switch (strtolower($payment_channel_arr[1])) {
						case 'bca':
							$name = 'BCAVA';
							break;
						case 'bni':
							$name = 'BNIVA';
							break;
						case 'bri':
							$name = 'BRIVA';
							break;
						case 'mandiri':
							$name = 'MANDIRIVA';
							break;
						case 'permata':
							$name = 'PERMATAVA';
							break;
						default:
							$name = 'BCAVA';
							break;
					}
					break;
				case 'cstore':
					switch (strtolower($payment_channel_arr[1])) {
						case 'indomaret':
							$name = 'INDOMARET';
							break;
						case 'alfamart':
							$name = 'ALFAMART';
							break;
						case 'fastpay':
							$name = 'FASTPAY';
							break;
					}
					break;
				case 'qris':
					switch (strtolower($payment_channel_arr[1])) {
						case 'qris':
							$name = 'QRISPAY';
							break;
					}
				case 'pulsa':
					switch (strtolower($payment_channel_arr[1])) {
						case 'telkomsel':
							$name = 'TCASH';
							break;
						case 'xl':
							$name = 'XLTUNAI';
							break;
						case 'indosat':
							$name = 'DOMPETKU';
							break;
					}
				case 'payment_code':
					switch (strtolower($payment_channel_arr[1])) {
						case 'atm137':
							$name = 'ATM137';
							break;
						case 'bebasbayar':
							$name = 'BEBASBAYAR';
							break;
						case 'cimbc':
							$name = 'CIMBC';
							break;
						case 'danamon':
							$name = 'DANAMON';
							break;
						case 'btnonline':
							$name = 'BTNONLINE';
							break;
						case 'briep':
							$name = 'BRIEP';
							break;
						case 'finpay':
							$name = 'FINPAY';
							break;
						case 'bcakp':
							$name = 'BCAKP';
							break;
						case 'kkwp':
							$name = 'KKWP';
							break;
						case 'mandiriec':
							$name = 'MANDIRIEC';
							break;
						case 'mandiripc':
							$name = 'MANDIRIPC';
							break;
						case 'mandiricp':
							$name = 'MANDIRICP';
							break;
						case 'muamalat':
							$name = 'MUAMALAT';
							break;
					}
					break;
			}
			return (object) ['name' => $name];
		}

		private function _getBankValue($value)
		{
			$arr = [
					'bca' => 'BCAVA',
					'bni' => 'BNIVA',
					'bri' => 'BRIVA',
					'mandiri' => 'MANDIRIVA',
					'permata' => 'PERMATAVA',
				];
			$search = array_search($value, $arr);
			if ($search == '') {
				$search = $value;
			}
			return $search;
		}
		*/

	/*=================================   End of Private functions   ==================================*/

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		try {
			$this->request['headers'] = [
					'Content-Type' => 'application/x-www-form-urlencoded',
					// 'Content-Type' => 'application/json',
					// 'Accept' => 'application/json',
					'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':' . $this->init->getSecret()),
				];
			$this->request['url'] = $this->init->getPaymentURL() . '/token';
			$this->request['data'] = [
					'api_key1' => $this->init->getMID(),
					'api_key2' => $this->init->getSecret()
				];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			$response = json_decode($response['content']);
			if (
				!empty($response->rc)
				&& $response->rc === '00'
			) {
				$content = $response;
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					$result = $content->data;
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

	public function RedirectPayment(Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(Transaction $transaction)
	{
		try {
			$this->transaction = $transaction;
			//
			$this->form['order_id'] =  $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['item'] = $this->transaction->getItem();
			$this->form['amount'] = $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtoupper(trim( $arr[0] ?? '' ));
			$payment_channel = strtoupper(trim( $arr[1] ?? '' ));
			// Create spi_signature
			$merchant_key = $this->init->getMerchantKey();
			$spi_token =  $this->init->getMID() . $this->init->getSecret();
			$spi_merchant_transaction_reff = $this->transaction->getOrderID();
			$spi_amount = number_format(doubleval($this->form['amount']), 2, ".", "");
			$spi_signature = strtoupper(sha1(
					$spi_token . '|' .
					$merchant_key . '|' .
					$spi_merchant_transaction_reff . '|' .
					$spi_amount .
					'|0|0'
				));
			//
			$token = $this->GetToken([]);
			$this->form['token'] = $token->token;
			$data = [
					'cms' => "WINPAY API",
					'spi_callback' => $this->init->getCallbackURL(),
					'url_listener' => $this->init->getReturnURL(),
					'spi_currency' => $this->form['currency'],
					'spi_item' => [
							[
									'name' => $this->form['item'],
									'sku' => $this->form['invoice_no'],
									'qty' => 1,
									'unitPrice' => $this->form['amount'],
									'desc' => $this->form['description'],
								],
						],
					'spi_amount' => $this->form['amount'],
					'spi_signature' => $spi_signature,
					'spi_token' => $spi_token,
					'spi_merchant_transaction_reff' => $spi_merchant_transaction_reff,
					'spi_billingPhone' => $this->form['customer_phone'],
					'spi_billingEmail' => $this->form['customer_email'],
					'spi_billingName' => $this->form['customer_name'],
					'spi_paymentDate' =>
						date("YmdHis",
							strtotime(date('H:i:s')) + (60 * $this->transaction->getExpireAt())
						),
				];
			// Overides & insertion
			switch ($payment_channel) {
				case 'QRISPAY':
					$data = $data + [
							'spi_qr_type' => 'static',
							'spi_qr_fee_type' => 'percent',
							'spi_qr_fee' => '10',
						];
					break;
				default:
					$data = $data + [
							'get_link' => 'no',
						];
					break;
			}
			$this->form['data'] = $data;
			// Data
			$encrypted_message = $this->_OpenSSLEncrypt(
					json_encode($data),
					$token->token
				);
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = //preg_replace('#/+#','/',
					$this->init->getPaymentURL() . '/apiv2/' . $payment_channel;
				//);
			$this->request['headers'] = [
					'Content-Type' => 'application/x-www-form-urlencoded',
				];
			$this->request['data'] = [
					'orderdata' =>
						substr($encrypted_message, 0, 10) .
						$token->token .
						substr($encrypted_message, 10)
				];
			$this->request['option'] = [
					'as_json' => false,
				];
			$post = $this->DoRequest('POST',  $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					// Success
					/*
					// VA
					{
						"rc": "00",
						"rd": "Transaksi Anda sedang dalam proses, Segera lakukan pembayaran menggunakan Mandiri Va sejumlah IDR Rp. 889.400- sebelum jam 2021-02-10 19:52, Order ID Anda adalah 888981000126806. RAHASIA Dilarang menyebarkan ke ORANG Tdk DIKENAL   Terimakasih",
						"request_time": "2021-02-10 17:52:11.078139",
						"data": {
							"reff_id": "303700501",
							"payment_code": "888981000126806",
							"order_id": "0012954330",
							"request_key": "",
							"url_listener": "https:\/\/vogame.dev\/returnUrl",
							"payment_method": "MANDIRI VIRTUAL ACCOUNT",
							"payment_method_code": "MANDIRIVA",
							"fee_admin": 0,
							"total_amount": 889400,
							"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/mandiriva?payid=8752f32167db210724be3beb0234ba53"
						},
						"response_time": "2021-02-10 17:52:13.485668"
					}
					// QRIS
					{
						"rc": "00",
						"rd": "QR Image is successfully generated",
						"request_time": "2021-02-10 15:07:47.452935",
						"data": {
							"spi_status_url": "https:\/\/secure-payment.winpay.id\/guidance\/index\/qrispay?payid=b7e211ba6014e2954e439246feb0424b",
							"payment_method": "Pembayaran QRIS",
							"payment_method_code": "QRISPAY",
							"fee_admin": 0,
							"total_amount": 64000,
							"order_id": "0012944467",
							"spi_hash": "b7e211ba6014e2954e439246feb0424b",
							"tips": 6400,
							"nominal_mdr": 448,
							"image_qr": "https:\/\/secure-payment.winpay.id\/scqr\/get_image_qr?payid=b7e211ba6014e2954e439246feb0424b"
						},
						"response_time": "2021-02-10 15:07:48.160497"
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
							],
						];
				} else {
					throw new \Exception($content->rd);
				}
			} else {
				throw new \Exception($content);
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function InquiryPaymentChannel()
	{
		try {
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL();
			$this->request['headers'] = [
					'Content-Type' => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode(
							$this->init->getMID() . ':' . $this->init->getSecret()
						),
				];
			$this->request['data'] = [];
			$this->request['option'] = [
					'as_json' => false,
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					// Success
					/*
					{
						"rc": "00",
						"rd": "Success",
						"request_time": "2021-02-25 15:32:07.235263",
						"data": {
							"token": "000f1f4cb5118390cc2ec79af671d61719c6f7a74281b16c2e70ba485dcf1750",
							"products": {
								"clickpay": [{
									"payment_code": "BCAKP",
									"payment_name": "BCA Klik Pay",
									"payment_description": "Bayar dengan BCA Klik Pay",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bca-klikpay.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BCAKP",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BCAKP",
									"is_direct": false
								}, {
									"payment_code": "CIMBC",
									"payment_name": "CIMB Clicks",
									"payment_description": "Bayar dengan CIMB Clicks",
									"payment_logo": "https:\/\/secure-payment.speedcash.co.id\/img\/spi-cimb-clicks.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/CIMBC",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/CIMBC",
									"is_direct": false
								}, {
									"payment_code": "BTNONLINE",
									"payment_name": "Debit Online BTN",
									"payment_description": "Bayar dengan Debit Online BTN",
									"payment_logo": "https:\/\/secure-payment.plasamall.com\/img\/spi-btnonline.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BTNONLINE",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BTNONLINE",
									"is_direct": false
								}],
								"modern store": [{
									"payment_code": "ALFAMART",
									"payment_name": "Alfamart",
									"payment_description": "Bayar di gerai Alfamart",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-alfamart.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/ALFAMART",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/ALFAMART",
									"is_direct": true
								}, {
									"payment_code": "FASTPAY",
									"payment_name": "Fastpay",
									"payment_description": "Bayar di Outlet Fastpay",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-fastpay.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/FASTPAY",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/FASTPAY",
									"is_direct": true
								}],
								"bank transfer": [{
									"payment_code": "MANDIRIPC",
									"payment_name": "Mandiri Pay Code",
									"payment_description": "Bayar dengan Mandiri Payment Code",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-mandiri.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/MANDIRIPC",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/MANDIRIPC",
									"is_direct": true
								}, {
									"payment_code": "BCAPC",
									"payment_name": "ATM BCA",
									"payment_description": "Bayar di ATM BCA",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-atm-bca.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BCAPC",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BCAPC",
									"is_direct": true
								}],
								"virtual account": [{
									"payment_code": "BNIVA",
									"payment_name": "BNI VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan BNI VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bni-va.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BNIVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BNIVA",
									"is_direct": true
								}, {
									"payment_code": "BRIVA",
									"payment_name": "BRI VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan BRI VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-bri-va.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/BRIVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/BRIVA",
									"is_direct": true
								}, {
									"payment_code": "PERMATAVA",
									"payment_name": "PERMATA VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan PERMATA VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-permata-va.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/PERMATAVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/PERMATAVA",
									"is_direct": true
								}, {
									"payment_code": "MANDIRIVA",
									"payment_name": "MANDIRI VIRTUAL ACCOUNT",
									"payment_description": "Bayar dengan MANDIRI VIRTUAL ACCOUNT",
									"payment_logo": "https:\/\/secure-payment.winpay.id\/img\/spi-mandiriva.png",
									"payment_url": "https:\/\/secure-payment.winpay.id\/api\/MANDIRIVA",
									"payment_url_v2": "https:\/\/secure-payment.winpay.id\/apiv2\/MANDIRIVA",
									"is_direct": true
								}]
							}
						},
						"response_time": "2021-02-25 15:32:07.444127"
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
								],
						];
				} else {
					throw new \Exception($content->rd);
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
			"id_transaksi": "5757636",
			"no_reff": "7891092505",
			"response_code": "00",
			"id_produk": "SCPIMNDRCP",
			"method_code": "MANDIRICP",
			"keterangan": "Transaksi anda berhasil"
		}
		*/
		try {
			if (!empty($request)) {
				SELF::Validate($request, [
						'id_transaksi',
						'no_reff',
						'response_code',
					]);
				switch ($request->response_code) {
					case '00':
						$response_message = 'Success';
						break;
					case '01':
						$response_message = 'Access denied';
						break;
					case '04':
						$response_message = 'Data not found ';
						break;
					case '05':
						$response_message = 'General error';
						break;
					case '99':
						$response_message = 'Parameter not valid';
						break;
				}
				//
				if (
					!empty($content->rc)
					&& $content->rc == '00'
				) {
					/*
					incoming data
					{
						"rc":"00",
						"rd":"Transaksi Anda sedang dalam proses, Segera lakukan pembayaran menggunakan Mandiri Va sejumlah IDR Rp. 64.000- sebelum jam 2020-12-14 13:48, Order ID Anda adalah 888981000000649. RAHASIA Dilarang menyebarkan ke ORANG Tdk DIKENAL   Terimakasih",
						"request_time":"2020-12-14 11:48:27.480316",
						"data":{
							"reff_id":"4940517",
							"payment_code":"888981000000649",
							"order_id":"0007921310",
							"request_key":"",
							"url_listener":"https:\/\/ibank.growinc.dev\/oanwef4851ashrb\/pg\/dk\/redapi_form",
							"payment_method":"MANDIRI VIRTUAL ACCOUNT",
							"payment_method_code":"MANDIRIVA",
							"fee_admin":0,
							"total_amount":64000,
							"spi_status_url":"https:\/\/sandbox-payment.winpay.id\/guidance\/index\/mandiriva?payid=175ed66633c7bbb150dd046543e940aa"
						},
						"response_time":"2020-12-14 11:48:28.666029"
					}
					*/
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
					throw new \Exception('Callback check failed');
				}
			} else {
				throw new \Exception('Callback is empty');
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
			if (!empty($request)) {
				SELF::Validate($request, [
						'is_qris',
					]);
				// Go
				$this->request['time'] = time();
				$this->request['url'] = $this->init->getRequestURL() .
					($request->is_qris == 'no' ?
						'/check-wpi-transaction' :
						'/check-qris-transaction');
				$this->request['headers'] = [
						'Content-Type' => 'application/x-www-form-urlencoded',
						'Authorization' => 'Basic ' . base64_encode($this->init->getMID() . ':' . $this->init->getSecret()),
					];
				$this->request['data'] = [
						'order_id' => $request->order_id ?? '',
						// 'id_transaction' => $request->id_transaction ?? '',
						// 'id_transaction_inquiry' => $request->id_transaction_inquiry ?? '',
						// 'id_transaction_payment' => $request->id_transaction_payment ?? '',
					];
				$this->request['option'] = [
						'as_json' => false,
					];
				$get = $this->DoRequest('GET',  $this->request);
				$response = (array) $get['response'];
				extract($response);
				if (!empty($status_code) && $status_code === 200) {
					$content = (object) json_decode($content);
					if (
						!empty($content->rc)
						&& $content->rc == '00'
					) {
						// Success
						/*
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
									],
							];
					} else {
						throw new \Exception($content->rd);
					}
				} else {
					throw new \Exception($content);
				}
			} else {
				throw new \Exception('Request is empty');
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
