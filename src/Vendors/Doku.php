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
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->init->getPaymentURL();
			$this->request['data'] = [
					'MALLID' => $this->init->getMID(),
					'CHAINMERCHANT' => 'NA',
					'AMOUNT' => $this->form['amount'],
					'PURCHASEAMOUNT' => $this->form['amount'],
					'TRANSIDMERCHANT' => $this->form['order_id'],
					'PAYMENTTYPE' => 'SALE',
					'WORDS' => sha1(
							number_format($this->form['amount'], 2, '.', '') .
							$this->init->getMID() .
							$this->init->getSecret() .
							$this->form['order_id']
						),
					'REQUESTDATETIME' => date('YmdHis', $this->transaction->getTime()),
					'CURRENCY' => '360', // 'ID', 'IDN',
					'PURCHASECURRENCY' => '360', // 'ID', 'IDN',
					'SESSIONID' => 'test_session',
					'NAME' => $this->form['customer_name'],
					'EMAIL' => $this->form['customer_email'],
					'ADDITIONALDATA' => '',
					'BASKET' =>
						'item 1,' .
						number_format($this->form['amount'], 2, '.', '') . ',1,' .
						number_format($this->form['amount'], 2, '.', ''),
					'SHIPPING_ADDRESS' => '',
					'SHIPPING_CITY' => '',
					'SHIPPING_STATE' => '',
					'SHIPPING_COUNTRY' => '',
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
			$this->request['headers'] = [];
			$this->request['option'] = [
					'as_json' => false,
				];


			$this->request['url'] = 'http://103.5.45.182:13579/parse/' .
				'doku' . '/' .
				'all' . '/' .
				base64_encode(json_encode($this->request['url'])) . '/' .
				base64_encode(http_build_query($this->request['data']));

// print_r(json_encode($this->request['data']));
// // print_r(http_build_query($this->request['data']));
// exit();

			// Go
			$get = $this->DoRequest('GET', $this->request);
			$response = (array) $get['response'];
			extract($response);

print_r($get);



exit();

			$post = $this->DoRequest('POST', $this->request);
print_r($post);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				if (!empty($content)) {
				}
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
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