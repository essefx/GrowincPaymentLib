<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class GudangVoucher extends Requestor implements VendorInterface
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
		$this->transaction = $transaction;
		//
		$payment_url = $this->init->getPaymentURL() . '?'
			. 'merchantid=' . $this->init->getMID()
			. '&amount=' . (float) $this->transaction->getAmount()
			. '&product=' . $this->transaction->getItem()
			. '&custom=' . $this->transaction->getOrderID()
			. '&email=' . $this->transaction->getCustomerEmail()
			. '&signature=' . md5( $this->init->getMID() . $this->transaction->getAmount() . $this->init->getSecret() . $this->transaction->getOrderID() )
			. '&custom_redirect=' . $this->init->getCallbackURL();
		header('location: ' . $payment_url);
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
			// $this->form['description'] = $this->transaction->getDescription();
			//
			// $this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			// $this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			// $this->form['customer_address'] = $this->transaction->getCustomerAddress();
			// $this->form['country_code'] = $this->transaction->getCountryCode();
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			//
			$payment_url = $this->init->getPaymentURL() . '?'
				. 'merchantid=' . $this->init->getMID()
				. '&amount=' . (float) $this->form['amount']
				. '&product=' . $this->form['item']
				. '&custom=' . $this->form['order_id']
				. '&email=' . $this->form['customer_email']
				. '&signature=' . md5(
							$this->init->getMID() .
							$this->form['amount'] .
							$this->init->getSecret() .
							$this->form['order_id']
						)
				. '&custom_redirect=' . $this->init->getCallbackURL();
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $payment_url;
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
					// First segment
					$arr_p = [];
					$ps = $xpath->query('//p[@class="mb-1"]');
					foreach($ps as $p) {
						$arr_p[] = $p->textContent;
					}
					if (count($arr_p)) {
						$order_id = trim(explode(':', $arr_p[0])[1]);
						$amount = str_replace('.', '',
								trim(explode('Rp.',
									$arr_p[1]
								)[1])
							);
						$expired_at = trim(explode('sebelum', $arr_p[2])[1]);
					}
					// Second segment
					$as = $xpath->query('//div[@id="headingQR"]/h4/a/@href');
					foreach($as as $a) {
      				$gv_payment_url = $a->value;
					}
					// Third segment
					$arr_bank = [];
					$tables = $xpath->query('//table');
					for ($i=0; $i<count($tables); $i++) {
						$bank_name = trim(
							$xpath->query('./tr[1]/td[1]', $tables[$i])->item(0)->textContent
						);
						$bank_name = str_replace('Bank ', '', $bank_name);
						$bank_name = str_replace(' ', '_', $bank_name);
						$bank_name = preg_replace('/[^A-Za-z0-9\-_]/', '', $bank_name); // Removes special chars.
						$bank_name = strtolower($bank_name);
						if ($payment_method == 'bank_transfer') {
							if ($bank_name == $payment_channel) {
								$fee = trim(
									preg_replace("/[^0-9]/", "",
										$xpath->query('./tr[1]/td[2]', $tables[$i])->item(0)->textContent
									)
								);
								$pay_code = trim(
									preg_replace("/[^0-9]/", "",
										$xpath->query('./tr[2]/td[1]', $tables[$i])->item(0)->textContent
									)
								);
								$amount = trim(
									preg_replace("/[^0-9]/", "",
											$xpath->query('./tr[2]/td[2]', $tables[$i])->item(0)->textContent
									)
								);
							}
						}
						// if (empty($payment_method) && empty($payment_channel)) {
							$arr_bank[$i]['amount'] = (float) trim(
								preg_replace("/[^0-9]/", "",
										$xpath->query('./tr[2]/td[2]', $tables[$i])->item(0)->textContent
								)
							);
							$arr_bank[$i]['bank_code'] = $bank_name;
							$arr_bank[$i]['fee'] = (float) trim(
								preg_replace("/[^0-9]/", "",
									$xpath->query('./tr[1]/td[2]', $tables[$i])->item(0)->textContent
								)
							);
							$arr_bank[$i]['pay_code'] = trim(
								preg_replace("/[^0-9]/", "",
									$xpath->query('./tr[2]/td[1]', $tables[$i])->item(0)->textContent
								)
							);
						// }
					}
					// Return
					if (empty($payment_method) && empty($payment_channel)) {
						// Show all options
						$res = [
								'status' => '000',
								'data' => (array) [
										'order_id' => $order_id,
										'amount' => (float) $amount,
										//
										'all_pay_codes' => $arr_bank,
										//
										'gv_payment_url' => $gv_payment_url,
										'payment_url' => $payment_url,
										'expired_at' => date('Y-m-d H:i:s', strtotime($expired_at)),
									],
							];
					} else {
						// Only filtered channel
						$res = [
								'status' => '000',
								'data' => (array) [
										'order_id' => $order_id,
										'amount' => (float) $amount,
										//
										'bank_code' => $payment_channel,
										'fee' => (float) $fee,
										'pay_code' => $pay_code,
										//
										'all_pay_codes' => $arr_bank,
										//
										'gv_wallet_payment_url' => $gv_payment_url,
										'payment_url' => $payment_url,
										'expired_at' => date('Y-m-d H:i:s', strtotime($expired_at)),
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

	public function Callback(object $request)
	{
		// Example incoming data callback payment
		/*
		{
			"data": "878<\/merchant_id>VPAY<\/merchant>87820210208154546<\/reference>0012773633<\/voucher_code>Apple<\/purpose>0012773633<\/custom>SUCCESS<\/status>YES<\/development><\/trans_doc>"
		}
		*/
		try {
			if (isset($request->data) && !empty($request->data)) {
				$xml = new \SimpleXMLElement(
						preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $request->data)
					);
				if (count($xml->xpath('//trans_doc'))) {
					$content = $xml->xpath('//trans_doc')[0];
					// XML content
					/*
					SimpleXMLElement Object
					(
						[merchant_id] => 878
						[merchant] => VPAY
						[reference] => 87820210208154546
						[voucher_code] => 0012773633
						[amount] => SimpleXMLElement Object
							(
									[@attributes] => Array
										(
											[currency] => IDR
											[nominal] => 560000
										)

							)

						[purpose] => Apple
						[custom] => 0012773633
						[status] => SUCCESS
						[development] => YES
					)
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
					throw new \Exception('Callback XML is empty');
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
					'order_id'
				]);
			//
			$request_url = $this->init->getRequestURL()
				. '?'
				. 'merchantid=' . $this->init->getMID()
				. '&custom=' . $request->order_id
				. '&signature=' . md5(
							$this->init->getSecret() .
							$this->init->getMID() .
							$request->order_id
						);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = preg_replace('#/+#','/', $request_url);
			$this->request['data'] = [
					'merchantid' => $this->init->getMID(),
					'custom' => $request->order_id,
					'signature' => md5(
							$this->init->getSecret() .
							$this->init->getMID() .
							$request->order_id
						),
				];
			$this->request['headers'] = [];
			$this->request['option'] = [
					'as_json' => false,
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (
					!isset($content->error_code)
					&& !isset($content->message)
				) {
					// Success
					/*
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
