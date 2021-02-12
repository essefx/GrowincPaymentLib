<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Faspay extends Requestor implements VendorInterface
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
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['item'] = $this->transaction->getItem();
			$this->form['amount'] =
				(float) $this->transaction->getAmount() * 100;
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['country_code'] = $this->transaction->getCountryCode();
			//
			$this->form['payment_channel'] = $this->transaction->getPaymentMethod();
			//

			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->init->getPaymentURL();
			$this->request['data'] = [
					'request' => 'Post Data Transaction',
					'merchant_id' => explode(' : ', $this->init->getMID())[1],
					'merchant' => explode(' : ', $this->init->getMID())[0],
					'signature' => sha1(md5(
							explode(' : ', $this->init->getSecret())[0] .
							explode(' : ', $this->init->getSecret())[1]
						)),
					'bill_no' => $this->form['order_id'],
					'bill_reff' => $this->form['invoice_no'],
					'bill_date' => date('Y-m-d H:i:s', $this->transaction->getTime()),
					'bill_expired' => date("Y-m-d\TH:i:s",  $this->transaction->getTime() + (3600 * $this->transaction->getExpireAt())),
					'bill_desc' => $this->form['description'],
					'bill_currency' => $this->form['currency'],
					// 'bill_gross' => 0,
					// 'bill_miscfee' => 0,
					'bill_total' => $this->form['amount'],
					'payment_channel' => $this->form['payment_channel'],
					// Payment type :
					// 1: Full Settlement
					// 2: Installment
					// 3: Mixed 1 & 2
					// Pay Type 2 & 3 only implement on BCA KlikPay channel
					'pay_type' => '1',
					'cust_no' => $this->form['customer_email'],
					'cust_name' => $this->form['customer_name'],
					// Customer User ID on bank’s services (ex : KlikBCA User Id)
					// 'bank_user_id' => '',
					'msisdn' => $this->form['customer_phone'],
					'email' => $this->form['customer_email'],
					'terminal' => '10', // Always use 10 for Terminal
					'billing_name' => $this->form['customer_name'],
					'billing_lastname' => '',
					'billing_address' => $this->form['customer_address'],
					'billing_address_city' => '',
					'billing_address_region' => '',
					'billing_address_state' => '',
					'billing_address_poscode' => '',
					'billing_address_country_code' => '',
					'receiver_name_for_shipping' => '',
					'shipping_lastname' => '',
					'shipping_address' => '',
					'shipping_address_city' => '',
					'shipping_address_region' => '',
					'shipping_address_state' => '',
					'shipping_address_poscode' => '',
					'shipping_address_country_code' => '',
					'shipping_msisdn' => '',
					'item' => [
							'product' => $this->form['item'],
							'amount' => $this->form['amount'],
							'qty' => '1',
							// Payment plan
							// 1: Full Settlement
							// 2: Installement
							'payment_plan' => '1',
							// Installment Tenor
							// 00: Full Payment
							// 03: 3 months
							// 06: 6 months
							// 12: 12 months
							// Tenor 03,06,12 only use on BCA KlikPay channel
							'tenor' => '00',
							// Merchant Id From Payment Channel ex : MID from BCA KlikPay
							'merchant_id' => explode(' : ', $this->init->getMID())[1],
						],
					'reserve1' => '',
					'reserve2' => '',
					'signature' => sha1(md5(
							explode(' : ', $this->init->getSecret())[0] .
							explode(' : ', $this->init->getSecret())[1] .
							$this->form['order_id']
						)),
				];
			$this->request['headers'] = [
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
				];
			$this->request['option'] = [
					'as_json' => true,
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				// Parse data
				$content = (object) json_decode($content);
				if (!empty($content)) {
					if (
						!empty($content->response_code)
						&& $content->response_code == '00'
					) {
						$res = [
								'status' => '000',
								'data' => (array) array_merge((array) $content, ['_real_amount' => (float) $this->form['amount'] / 100]),
							];
					} else {
						$res = [
								'status' => $content->response_code,
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
					throw new \Exception("Parsed data is empty", 1);
				}
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function ParseQR($payment_url)
	{
		try {
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $payment_url;
			$this->request['data'] = [];
			$this->request['headers'] = [];
			$this->request['option'] = [
					'as_json' => false,
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				if (!empty($content)) {
					// HTML Dom
					$doc = new \DOMDocument();
					libxml_use_internal_errors(true);
					$doc->loadHTML($content);
					libxml_clear_errors();
					$xpath = new \DOMXpath($doc);
					// Get all TR
					$trs = $xpath->query('//tr');
					foreach ($trs as $tr) {
						$img = $xpath->query('//img[@class="qr-code"]/@src', $tr);
					}
					if (!empty($img->item(0)->nodeValue)) {
						$qr_code = $img->item(0)->nodeValue;
					}
					// Return
					if (!empty($qr_code)) {
						// Show all options
						$res = [
								'status' => '000',
								'data' => (array) [
										'payment_url' => $payment_url,
										'qr_code' => $qr_code,
									],
							];
					} else {
						$res = [
								'status' => '000',
								'data' => (array) [
										'payment_url' => $payment_url,
										'qr_code' => '',
									],
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
					throw new \Exception("Parsed data is empty", 1);
				}
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}

	public function ParsePaymentPage($channel_code, $payment_url, $param = '')
	{
		try {
			$this->request['url'] = 'http://103.5.45.182:13579/parse/' .
				'faspay' . '/' .
				$channel_code . '/' .
				base64_encode($payment_url) . '/' . $param;
			// Go
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				// Parse data
				$content = (object) json_decode($content);
				if (
					isset($content) && !empty($content)
				) {
					if (
						!empty($content->status)
						&& $content->status == '000'
					) {
						// Success
						/*
						{
							"status": "000",
							"data": {
								"payment_url": "https://dev.faspay.co.id/pws/100003/0830000010100000/a377f2971f1f2d666e14d611fef2ed7af21cdec0?trx_id=3366080100000018&merchant_id=33660&bill_no=1612769989",
								"va_number": "3366080100000018"
							}
						}
						*/
						$res = [
								'status' => '000',
								'data' => (array) $content->data,
							];
					} else {
						$res = [
								'status' => $content->status,
								'data' => (array) $content->data,
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
					throw new \Exception("Parsed data is empty", 1);
				}
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
			$this->request['url'] = preg_replace('#/+#','/', $this->init->getRequestURL());
			$this->request['data'] = [
					'request' => 'Request List of Payment Gateway',
					'merchant_id' => explode(' : ', $this->init->getMID())[1],
					'merchant' => explode(' : ', $this->init->getMID())[0],
					'signature' => sha1(md5(
							explode(' : ', $this->init->getSecret())[0] .
							explode(' : ', $this->init->getSecret())[1]
						)),
				];
			$this->request['headers'] = [
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
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
					!empty($content->response_code)
					&& $content->response_code == '00'
				) {
					// Success
					/*
					{
						"response": "Request List of Payment Gateway",
						"merchant_id": "33660",
						"merchant": "VoGame Indonesia",
						"payment_channel": [{
							"pg_code": "807",
							"pg_name": "Akulaku"
						}, {
							"pg_code": "801",
							"pg_name": "BNI Virtual Account"
						}, {
							"pg_code": "825",
							"pg_name": "CIMB VA"
						}, {
							"pg_code": "701",
							"pg_name": "DANAMON ONLINE BANKING"
						}, {
							"pg_code": "708",
							"pg_name": "Danamon VA"
						}, {
							"pg_code": "302",
							"pg_name": "LinkAja"
						}, {
							"pg_code": "802",
							"pg_name": "Mandiri Virtual Account"
						}, {
							"pg_code": "814",
							"pg_name": "Maybank2U"
						}, {
							"pg_code": "408",
							"pg_name": "MAYBANK VA"
						}, {
							"pg_code": "812",
							"pg_name": "OVO"
						}, {
							"pg_code": "402",
							"pg_name": "Permata"
						}, {
							"pg_code": "711",
							"pg_name": "Shopee Pay"
						}, {
							"pg_code": "818",
							"pg_name": "Sinarmas Virtual Account"
						}, {
							"pg_code": "420",
							"pg_name": "UNICount-Rupiah"
						}],
						"response_code": "00",
						"response_desc": "Sukses"
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
					throw new \Exception($content->response_error->response_desc);
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
		{
			"request": "Payment Notification",
			"trx_id": "3366082500000174",
			"merchant_id": "33660",
			"merchant": "VoGame Indonesia",
			"bill_no": "1612782267",
			"payment_reff": "57910067322",
			"payment_date": "2021-02-08 18:11:35",
			"payment_status_code": "2",
			"payment_status_desc": "Payment Sukses",
			"bill_total": "6894",
			"payment_total": "6894",
			"payment_channel_uid": "825",
			"payment_channel": "CIMB VA",
			"signature": "ebebca6942f75a3c1fd72120c3ebbad384c973c0"
		}
		*/
		try {
			$signature = sha1(md5(
					explode(' : ', $this->init->getSecret())[0] .
					explode(' : ', $this->init->getSecret())[1] .
					$request->bill_no .
					$request->payment_status_code
				));
			if (!empty($request)) {
				if (strcmp($request->signature, $signature) === 0) {
					$content = (array) $request;
					// Amount reformat
					/*
					array_walk_recursive($content, function (&$v, $k) {
						if (
							$k == 'bill_total'
							|| $k == 'payment_total'
						) {
							$v = $v / 100;
						}
					});
					*/
					// Go
					$res = [
							'status' => '000',
							'data' => (array) $content,
							// 'data' => (array) array_merge($content, ['_real_amount' => (float) ($content['bill_total'] / 100)]),
						];
					$result = [
							'request' => (array) $content,
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
			SELF::Validate($request, [
					'order_id',
					'trx_id',
				]);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = preg_replace('#/+#','/', $this->init->getRequestURL());
			$this->request['data'] = [
					'request' => 'Inquiry Status Payment',
					'trx_id' => $request->trx_id,
					'merchant_id' => explode(' : ', $this->init->getMID())[1],
					'bill_no' => $request->order_id,
					'signature' => sha1(md5(
							explode(' : ', $this->init->getSecret())[0] .
							explode(' : ', $this->init->getSecret())[1] .
							$request->order_id
						)),
				];
			$this->request['headers'] = [
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
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
					!empty($content->response_code)
					&& $content->response_code == '00'
				) {
					// Success
					/*
					{
						"status": "000",
						"data": {
							"response": "Inquiry Status Payment",
							"trx_id": "3366082500000034",
							"merchant_id": "33660",
							"merchant": "VoGame Indonesia",
							"bill_no": "1612767691",
							"payment_reff": "",
							"payment_date": "",
							"payment_status_desc": "Belum diproses",
							"payment_status_code": "0",
							"payment_total": "",
							"response_code": "00",
							"response_desc": "Sukses"
						}
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
					throw new \Exception($content->response_error->response_desc);
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
