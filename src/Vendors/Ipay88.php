<?php

namespace Growinc\Payment\Vendors;


use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;



class Ipay88 extends Requestor implements VendorInterface {

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
  // function hex2bin($hexSource)
  // {
  //    for ($i=0;$i<strlen($hexSource);$i=$i+2)
  //    {
  //       $bin .= chr(hexdec(substr($hexSource,$i,2)));
  //    }

  //    return $bin;
  // }

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		try {

			$this->transaction = $transaction;

			// $this->form['merchant_code'] = "ID01625";

			/*	paymentId:
			ATM Transfer | payment method
			  - Maybank VA -> 9
			  - Mandiri ATM -> 17
			  - BCA VA -> 25
			  - BNI VA -> 26
			  - Permata VA -> 31
			*/

			$this->request['time'] = time();

			$transaction_date =  date("Y-m-d h:i:s");
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['payment_method'] = $this->transaction->setPaymentMethod();
				$this->form['amount'] = $this->transaction->getAmount();
				$this->form['description'] = $this->transaction->getDescription();
			$this->form['currency'] = $this->transaction->getCurrency();
			$this->form['no_ref'] = $this->transaction->getOrderID();
				//
				$this->form['customer_name'] = $this->transaction->getCustomerName();
				$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['response_url'] = $this->init->getResponseUrl();
			$this->form['backend_url'] = $this->init->getBackendURL();

			/**
			* Billing Address
			* get data  : customer_name, customer_email, customer_phone, country_code
			*/

			$this->form['billing_address'] = [
				'first_name' => $this->form['customer_name'],
				'last_name' => 'IPSUM',
				'address' => 'sudirman',
				'city' => 'Jakarta',
				'postal_code' => '12190',
				'phone' => $this->form['customer_phone'],
				'country_code' => 'IDN',
				'email' => $this->form['customer_email'],
			];

			/**
			* Shipping Address
			* get data  : shipping_address, customer_name, customer_email,  customer_phone, country_code
			*/

			$this->form['shipping_address'] = [
				'first_name' => $this->form['customer_name'],
				'last_name' => 'IPSUM',
				'address' => 'sudirman',
				'city' => 'Jakarta',
				'postal_code' => '12190',
				'phone' => $this->form['customer_phone'],
				'country_code' => 'IDN',
				'email' => $this->form['customer_email']
			];

			/**
			* Detail Seller
			* get data  : customer_name, customer_phone, customer_email,  customer_phone, country_code
			*/

			$this->form['detail_seller'] = [
				'first_name' => $this->form['customer_name'],
				'last_name' => 'IPSUM',
				'address' => 'sudirman',
				'city' => 'Jakarta',
				'postal_code' => '12190',
				'phone' => $this->form['customer_phone'],
				'country_code' => 'IDN',
				'email' => $this->form['customer_email']
			];

			$this->form['payment_url'] = $this->init->getPaymentURL() . '/ePayment/WebService/PaymentAPI/Checkout';

			// $this->form['seller'] = $this->transaction->getSeller();

			// item transaksi
			$this->form['item_details'] = $this->transaction->getItem();
			$amountTotal  = 0 ;
			foreach($this->form['item_details'] as $price){
					$amountTotal += (int) $price['price'] * (int) $price['quantity'] ;
				}

			// Create Signature
			$signature = $this->init->getSecret().$this->init->getMID().$this->transaction->getOrderID().$amountTotal.$this->form['currency'];
			$encode_signature = base64_encode(hex2bin(sha1($signature)));

			$this->init->setSign($encode_signature);
			// $signature__aa = openssl_digest($encode_signature, 'sha512');

			$this->request['url'] = $this->form['payment_url'];

			$this->request['data'] = [
				'MerchantCode' => $this->init->getMID(),
				'PaymentId' => (string) $this->form['payment_method'],
				'Currency' => $this->form['currency'],
				'RefNo' =>  $this->form['order_id'],
				// 'RefNo' =>  '0004805330',
				'Amount' => (int) $amountTotal,
				'ProdDesc' => $this->form['description'],
				'UserName' => $this->form['customer_name'],
				'UserEmail' => $this->form['customer_email'],
				'UserContact' => $this->form['customer_phone'],
				'Signature' => $encode_signature,
				'ResponseURL'=> $this->form['response_url'],
				'BackendURL'=>  $this->form['response_url'],
				'Remark'	=> 'Transaction '.$transaction_date,
				'Lang' => 'UTF-8',
				'itemTransactions' =>   $this->form['item_details'],
				'ShippingAddress' => $this->form['shipping_address'],
				'BillingAddress' => $this->form['billing_address'],
				'Sellers' => $this->form['detail_seller']
			];

			$this->request['headers'] = [[
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			]];

			$this->request['option'] = [
				'to_json' => true,
			];
			// Send request
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200)
			{
				$content = (object) json_decode($content);
				if (empty($content->ErrDesc) && $content->ErrDesc === "")
				{
					// Success
					// [
					//   {
					//   Status: "6",
					//   ErrDesc: "",
					//   MerchantCode: "ID01625",
					//   PaymentId: "9",
					//   Currency: "IDR",
					//   RefNo: "12345670",
					//   Amount: "250000",
					//   Remark: "Transaction 2020-11-08 12:39:54",
					//   Signature: "66SXTAZqOD1Nqy2kCtoCXugO6Ho=",
					//   xfield1: "",
					//   TransId: "T0050683000",
					//   AuthCode: "",
					//   VirtualAccountAssigned: "7828705000001580",
					//   TransactionExpiryDate: "09-11-2020 00:40",
					//   CheckoutURL: "https://sandbox.ipay88.co.id/epayment/entryv3.asp?CheckoutID=68ca4745a0156b368bbb7195400efea3fdc3c1fa3565c5cfd4755d57a31350a5&amp;Signature=fbiyE7gXDiVum8ip1YtbK0ctosE%3d"
					//   },
					//   "1"
					// ]

					$content = [
						'status' => '000',
						'data' => (array) $content,
					];

					$result = [
						'request' => (array) $this->request,
						'response' => [
						  'content' => json_encode($content),
						  'status_code' => 200,
						],
					];
				} else {
					throw new \Exception($content->ErrDesc);
					//  return print_r($resp);
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
    // Inapplicable
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


	public function Notification(object $request)
	{
		try {
			// print_r($request);
			$signature = $this->init->getSecret().$request->MerchantCode.$request->RefNo.$request->Amount.$request->Currency;
			$encode_signature = base64_encode(hex2bin(sha1($signature)));
			// print_r([$encode_signature, $request->Signature]);exit();

			if ($encode_signature === $request->Signature) {
			  $result = 'RECEIVEOK';
				$content = [
						'status' => '000',
						'data' => (array) $request,
					];
				$result = [
						'request' => (array) $request,
						'response' => [
								'content' => $result,
								'status_code' => 200,
							],
					];
			} else {
				throw new \Exception('Signature check failed');
			}

			// $result = '';

		}catch (\Throwable $e) {
		  throw new \Exception($this->ThrowError($e));
		}
		return $result ?? [];
	}



}