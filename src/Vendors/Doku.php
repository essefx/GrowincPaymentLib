<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Doku extends Requestor implements VendorInterface
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
			$this->form['amount'] = (float) $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['country_code'] = $this->transaction->getCountryCode();
			//
			$arr = explode(',', $this->transaction->getPaymentMethod());
			$payment_method = strtolower(trim( $arr[0] ?? '' ));
			$payment_channel = strtolower(trim( $arr[1] ?? '' ));
			// Data
			$this->form['words'] =
				number_format($this->form['amount'], 2, '.', '') .
				$this->init->getMID() .
				$this->init->getSecret() .
				$this->form['order_id'];
			$this->request['data'] = [
				'MALLID' => $this->init->getMID(),
				'CHAINMERCHANT' => 'NA',
				'AMOUNT' => $this->form['amount'] . '.00',
				'PURCHASEAMOUNT' => $this->form['amount'] . '.00',
				'TRANSIDMERCHANT' => $this->form['order_id'],
				'PAYMENTTYPE' => 'SALE',
				'WORDS' => sha1($this->form['words']),
				'REQUESTDATETIME' => date('YmdHis', $this->transaction->getTime()),
				'CURRENCY' => '360', // 'ID', 'IDN',
				'PURCHASECURRENCY' => '360', // 'ID', 'IDN',
				'SESSIONID' => 'doku_sessid',
				'NAME' => $this->form['customer_name'],
				'EMAIL' => $this->form['customer_email'],
				'ADDITIONALDATA' => $this->transaction->getDescription(),
				'BASKET' =>
					$this->transaction->getItem() . ',' .
					number_format($this->form['amount'], 2, '.', '') . ',1,' .
					number_format($this->form['amount'], 2, '.', ''),
				'SHIPPING_ADDRESS' => $this->form['customer_address'],
				'SHIPPING_CITY' => '',
				'SHIPPING_STATE' => '',
				'SHIPPING_COUNTRY' => $this->form['country_code'],
				'SHIPPING_ZIPCODE' => '',
				'PAYMENTCHANNEL' => '',
				'ADDRESS' => $this->form['customer_address'],
				'CITY' => '',
				'STATE' => '',
				'COUNTRY' => $this->form['country_code'],
				'ZIPCODE' => '',
				'HOMEPHONE' => '',
				'MOBILEPHONE' => $this->form['customer_phone'],
				'WORKPHONE' => '',
				'BIRTHDATE' => '',
			];
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->init->getPaymentURL();
			$this->request['headers'] = [
				//
			];
			$this->request['option'] = [
				// 'as_json' => false,
			];
			// print_r($this->form); exit();
			$this->request['url'] =
				SELF::CleanURL(
					$this->init->getRequestURL() . '/' .
					'doku' . '/' .
					$payment_method . '/' .
					$payment_channel . '/' .
					base64_encode($this->init->getPaymentURL()) . '/' .
					base64_encode(json_encode($this->request['data']))
				);
			// Go
			$get = $this->DoRequest('GET', $this->request);
			// print_r($get);
			$response = (array) $get['response'];
			extract($response);
			if (!empty($status_code) &&
				$status_code === 200
			) {
				$content = (object) json_decode($content);
				if (
					!empty($content->status)
					&& $content->status !== 000
				) {
					/* // Success VA
					{
						"status": "000",
						"data": {
							"payment_url": "https://staging.doku.com/Suite/Receive",
							"bank": "danamon",
							"va_number": "8920000010045174"
						}
					}
					*/
					$res = $content;
					$res = [
						'status' => '000',
						'data' => (array) $content,
					];
				} else {
					throw new \Exception('Parse unsuccessful', 901);
				}
			} else {
				throw new \Exception('Parse failed', 902);
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
		// Inapplicable
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