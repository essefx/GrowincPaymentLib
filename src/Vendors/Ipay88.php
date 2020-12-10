<?php

namespace Growinc\Payment\Vendors;


use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;
use Growinc\Payment\Transaction;


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

	public function SecurePayment(Transaction $transaction)
	{
		try{
	
			$this->transaction = $transaction;
			//
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['amount'] = $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			$this->form['currency'] = $this->transaction->getCurrency();
			
			/* Optional */ 
			$this->form['no_ref'] = $this->form['order_id'];
			
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['country_code'] = $this->transaction->getCountryCode();
		
			//
			$this->form['billing_address'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => 'IPSUM',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => 'sudirman',
					'city' => 'Jakarta',
					'postal_code' => '12190',
					'country_code' => $this->form['country_code'],
				];
			$this->form['shipping_address'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => 'IPSUM',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => 'sudirman',
					'city' => 'Jakarta',
					'postal_code' => '12190',
					'country_code' => $this->form['country_code'],
				];
			$this->form['customer_details'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => 'IPSUM',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'billing_address' => $this->form['billing_address'],
					'shipping_address' => $this->form['shipping_address'],
				];
				
			$this->form['item_details'] = $this->transaction->getItem();

			$this->form['customer_userid'] = $this->transaction->getCustomerUserid();
				
			$paymentMethode =  explode(',', $this->transaction->getPaymentMethod());
			$this->form['payment_type'] =  $this->getPayId($paymentMethode);
		
			$this->form['payment_url'] = $this->init->getPaymentURL();
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
			
			
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];

			// amount
			$amountTotal = 0;
			foreach($this->form['item_details'] as $price){
				$amountTotal += (int) $price['price'] * (int) $price['quantity'] ;
			}

			$signature = $this->init->getSecret().$this->init->getMID().$this->transaction->getOrderID().$amountTotal.$this->form['currency'];
      $encode_signature = base64_encode(hex2bin(sha1($signature)));

			$this->request['data'] = [
		        'MerchantCode' => $this->init->getMID(),
		        'PaymentId' => $this->form['payment_type']->id,
		        'Currency' => $this->form['currency'],
		        'RefNo' =>  $this->transaction->getOrderID(),
		        'Amount' => (int) $amountTotal, 
		        'ProdDesc' => $this->form['description'], 
		        'UserName' => $this->form['customer_name'],
		        'UserEmail' => $this->form['customer_email'],
		        'UserContact' => $this->form['customer_phone'],
		        'Signature' => $encode_signature,
		        'ResponseURL'=> $this->form['payment_url'],
		        'BackendURL'=>  $this->form['payment_url'],
		        'Remark'	=> 'Transaction ',
				'Lang' => 'UTF-8',
				'item_details' => $this->form['item_details'],
				'transaction_details' => [
					'order_id' => $this->form['order_id'],
					'gross_amount' => $amountTotal,
				],
				'customer_details' => [
					'email' => $this->form['customer_email'],
					'first_name' => $this->form['customer_name'],
					'last_name' => '',
					'phone' => $this->form['customer_phone'],
				],
			];
			
		
			/* HEADER*/ 
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
				'Authorization' => '',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			];

			$this->request['option'] = [
				'as_json' => true,
			];
		
			$post = $this->DoRequest('POST', $this->request);
			
	

			$response = (array) $post['response'];
		
			extract($response);
			$content = (object) json_decode($content);
		
			if ($content->Status != "") {
				if (empty($content->ErrDesc) && $content->ErrDesc === "") {

					$content = [
						'status' => '000',
						'data' => (array) $content,
					];

					$result = [
							'request' => (array) $this->request,
							'response' => [
								'content' => json_encode($content),
								'status_code' =>  $content['data']['Status'] == 6 ? 200 : 302 ,
								'va_number' => $content['data']['VirtualAccountAssigned'],
								'bank_code' => $this->form['payment_type']->name,
								'amount' => $content['data']['Amount'],
								'transaction_id' => $content['data']['TransId'], // vendor transaction_id
								'order_id' => $content['data']['RefNo'], // PGA order_id
								'payment_type' => $paymentMethode[0], // Payment Method
								'checkout' => $content['data']['CheckoutURL'], // Payment URL
								'transaction_status' => $content['data']['ErrDesc'] == "" ? 'pending' : $content['data']['ErrDesc'],
							],
					];
						
				} else {
					throw new \Exception($content->ErrDesc);
				}
			}

		}	catch (\Throwable $e) {
      throw new \Exception($this->ThrowError($e));
    }
		return $result ?? [];
	}

	/**
	 * @param string
	 * @return $result ? []
	 * */ 
	public function getPayId($paymentId){
		switch (strtolower($paymentId[0])) {
			/* Bank Transfer */ 
			case 'bank_transfer':
			
				switch ($paymentId[1]) {
					case 'bca':
						$id = 25;
						$name = 'BCA';
					break;
					case 'maybank':
						$id = 9;
						$name = 'Maybank';
					break;
					case 'mandiri':
						$id = 17;
						$name = 'Mandiri';
					break;
					case 'bni':
						$id = 26;
						$name = 'BNI';
					break;
					case 'permata':
						$id = 31;
						$name = 'Permata';
					break;
					default:
						$id = 25;
						$name = 'BCA';
					break;
				}
			break;

			/* Bank Transfer */ 
			case 'internet_banking':
				switch ($paymentId[1]) {
					case 'bcakp':
						$id = 8;
						$name = 'BCA';
					break;
					case 'cimbkp':
						$id = 11;
						$name = 'CIMB';
					break;
					case 'muamalatkp':
						$id = 14;
						$name = 'Muamalat';
					break;
					case 'danamonkp':
						$id = 23;
						$name = 'Danamon';
					break;
					default:
						$id = 8;
						$name = 'BCA';
					break;
				}
			break;
		}
		return (object) ['id' => $id, 'name' => $name];
	}

	/**
	 * @param object
	 * @return $result ? []
	 * */ 
	public function Signature(object $object){
		try {
			$main = $this->init->getSecret().$this->init->getMID().$this->transaction->getOrderID().$amountTotal.$this->form['currency'];
			base64_encode(hex2bin(sha1($main)));
		} catch (\Throwable $th) {
			//throw $th;
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