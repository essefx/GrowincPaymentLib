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
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'Authorization' => 'Basic ' . base64_encode($args['api_key1'] . ':' . $args['api_key2']),
				];
			$this->request['url'] = $args['token_url'];
			$this->request['data'] = [
					'api_key1' => $args['api_key1'],
					'api_key2' => $args['api_key2']
				];
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			$response = json_decode($response['content']);
			if (!empty($response->rc) && $response->rc === '00') {
				$content = $response;
				if (!empty($content->rc) && $content->rc == '00') {
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
			$token = $this->GetToken([
					'api_key1' => $this->init->getMID(),
					'api_key2' => $this->init->getSecret(),
					'token_url' => $this->init->getPaymentURL() . '/token',
				]);
			$this->form['token'] = $token;
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
			$this->request['url'] = preg_replace('#/+#','/',
					$this->init->getPaymentURL() . '/apiv2/' . $payment_channel
				);
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
			SELF::Validate($request, ['no_reff', 'id_transaksi']);
			$input = $request->no_reff . $request->id_transaksi;
			$get = $this->init->getSecret() . $this->init->getMID();
			// print_r($get);exit();
			$transID = $request->id_transaksi;
			// $spi_signature = strtoupper(sha1( $spi_token . '|' . $merchant_key . '|' . $spi_merchant_transaction_reff . '|' . $spi_amount . '|0|0' ));
			// print_r($signature);exit();

			if (strcmp($transID, $request->id_transaksi) === 0) {
				$content = [
					'status' => '000',
					'data' => (array) $request,
				];

				/*
				00	Success
				01	Access Denied! not authorized
				04	Data not found
				05	General Error
				99	Parameter not valid

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
				if ($content['data']['response_code'] == 00) {
					$status = 'success';
				} else if ($content['data']['response_code'] == 01) {
					$status = 'access denied !';
				} else if ($content['data']['response_code'] == 04) {
					$status = 'data not found ';
				} else if ($content['data']['response_code'] == 05) {
					$status = 'General Error ';
				} else if ($content['data']['response_code'] == 99) {
					$status = 'Parameter not valid';
				}

				$result = [
					'request' => (array) $request,
					'response' => [
						'content' => json_encode($content),
						'status_code' => 200,
						'bank_code' => $this->_getBankValue($content['data']['method_code']),
						// 'amount' => $content['data']['Amount'],
						'transaction_id' => $content['data']['id_transaksi'], // vendor transaction_id
						'order_id' => $content['data']['no_reff'], // PGA order_id
						'transaction_status' => $status,
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
