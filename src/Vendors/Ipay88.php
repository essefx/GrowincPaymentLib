<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;
use Growinc\Payment\Transaction;

class Ipay88 extends Requestor implements VendorInterface
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

	public function RedirectPayment(Transaction $transaction)
	{
		// Inapplicable
	}

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
			$this->form['postal_code'] = $this->transaction->getPostalCode();
			$this->form['city'] = $this->transaction->getCustomerCity();
		

			/** 
			* Billing Address
			*/

			$this->form['billing_address'] = [
				'first_name' => $this->form['customer_name'],
				'last_name' => 'IPSUM',
				'address' => $this->form['customer_address'],
				'city' => $this->form['city'] ?? 'Jakarta',
				'postal_code' => $this->form['postal_code'],
				'phone' => $this->form['customer_phone'],
				'country_code' => $this->form['country_code'] ?? 'IDN',
				'email' => $this->form['customer_email'],
			];

			/** 
			* Shipping Address
			*/

			$this->form['shipping_address'] = [
				'first_name' => $this->form['customer_name'],
				'last_name' => 'IPSUM',
				'address' => $this->form['customer_address'],
				'city' => $this->form['city'] ?? 'Jakarta',
				'postal_code' => $this->form['postal_code'],
				'phone' => $this->form['customer_phone'],
				'country_code' => $this->form['country_code'] ?? 'IDN',
				'email' => $this->form['customer_email']
			];
				/** 
			* Sellers
			*/
			$this->form['sellers'] = $this->transaction->getSeller();
				
			$this->form['item_details'] = $this->transaction->getItem();
			$this->form['customer_userid'] = $this->transaction->getCustomerUserid();
			$paymentMethode = explode(',', $this->transaction->getPaymentMethod());
			$this->form['payment_type'] =  $this->getPayId($paymentMethode);
			$this->form['payment_url'] = $this->init->getPaymentURL();
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
			
			//
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];

			// set total amount 
			$amountTotal = 0;
			foreach($this->form['item_details'] as $price){
				$amountTotal += (int) $price['price'] * (int) $price['quantity'] ;
			}

			// generate signature 
			$signature = $this->init->getSecret().$this->init->getMID().$this->transaction->getOrderID().$amountTotal.$this->form['currency'];
      		$encode_signature = base64_encode(hex2bin(sha1($signature)));
			
			// request data  
			$this->request['data'] = [
		        'MerchantCode' 			=> $this->init->getMID(),
		        'PaymentId' 			=> $this->form['payment_type']->id,
		        'Currency' 				=> $this->form['currency'],
		        'RefNo' 				=>  $this->transaction->getOrderID(),
		        'Amount'				=> (int) $amountTotal, 
		        'ProdDesc'				=> $this->form['description'], 
		        'UserName'				=> $this->form['customer_name'],
		        'UserEmail'				=> $this->form['customer_email'],
		        'UserContact'			=> $this->form['customer_phone'],
		        'Remark'				=> 'Transaction ',
				'Lang'					=> 'UTF-8',
		        'ResponseURL'			=> $this->init->getResponseUrl(),
		        'BackendURL'			=> $this->init->getBackendURL(),
				'Signature' 			=> $encode_signature,
				"xfield1"				=> "",
				'itemTransactions'		 => $this->form['item_details'],
				'ShippingAddress' 		=> $this->form['shipping_address'],
				'BillingAddress' 		=> $this->form['billing_address'],
				'Sellers' 				=> $this->form['sellers'],
			];
			
			// print_r(json_encode($this->request['data']));exit();
			/* HEADER */ 
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
			
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if ($content->Status != "") {
					if (empty($content->ErrDesc) && $content->ErrDesc === "") {
						/* ewallet
							{
							   "Status":"6",
							   "ErrDesc":"",
							   "MerchantCode":"ID01625",
							   "PaymentId":"75",
							   "Currency":"IDR",
							   "RefNo":"0008276045",
							   "Amount":"250000",
							   "Remark":"Transaction ",
							   "Signature":"kWvzVi4X\/LFBB\/5s3df+8hFVwto=",
							   "xfield1":"",
							   "TransId":"T0051309800",
							   "AuthCode":"",
							   "VirtualAccountAssigned":"https:\/\/api.uat.wallet.airpay.co.id\/v3\/merchant-host\/qr\/download?qr=Kd2lofpylhA6xaiZxeDfdeTeFYLPu1p1xMZaWagtTG",
							   "CheckoutURL":"https:\/\/sandbox.ipay88.co.id\/epayment\/entryv3.asp?CheckoutID=72b89e6aeb89818296488ead3f963bcf799eca4cd6ae85906214c930d75abdb5&Signature=2IX6cemMjOUVPsiT9C55OwrlGX8%3d"
							}
						*/
						$content = [
							'status' => '000',
							'data' => (array) $content,
						];
						/**/ 
						if ($content['data']['Status'] == 6 ) {
							$status = 'pending';
						}
						$result = [
								'request' => (array) $this->request,
								'response' => [
									'content' => json_encode($content),
									'status_code' =>  $content['data']['Status'] == 6 ? 200 : 302 ,
									// 'va_number' => $content['data']['VirtualAccountAssigned'],
									// 'bank_code' => $this->form['payment_type']->name,
									// 'amount' => $content['data']['Amount'],
									// 'transaction_id' => $content['data']['TransId'], // vendor transaction_id
									// 'order_id' => $content['data']['RefNo'], // PGA order_id
									// 'payment_type' => $paymentMethode[0], // Payment Method
									// 'checkout' => $content['data']['CheckoutURL'], // Payment URL
									// 'transaction_status' =>  $status,
								],
						];
							
					} else {
						throw new \Exception($content->ErrDesc);
					}
				}
			} else {
				throw new \Exception($content);
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
				switch (strtolower($paymentId[1])) {
					case 'bca':
						$id = 30;
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
			/* Others */ 
			case 'others':
				switch (strtolower($paymentId[1])) {
					case 'alfamart':
						$id = 60;
						$name = 'alfamart';
					break;
					case 'indomaret':
						$id = 65;
						$name = 'indomaret';
					break;
					case 'akulaku':
						$id = 71;
						$name = 'akulaku';
					break;
					case 'indodana':
						$id = 70;
						$name = 'indodana';
					break;					
					case 'kredivo':
						$id = 55;
						$name = 'kredivo';
					break;
					default:
						$id = 60;
						$name = 'alfamart';
					break;
				}
			break;
			/* Internet Banking */ 
			case 'internet_banking':
				switch (strtolower($paymentId[1])) {
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
			/* Credit Card */ 
			case 'credit_card':
				switch (strtolower($paymentId[1])) {
					case 'bca':
						$id = 52;
						$name = 'cc_bca';
					break;
					case 'bri':
						$id = 35;
						$name = 'BRI';
					break;
					case 'cimb':
						$id = 42;
						$name = 'cimb';
					break;
					case 'cimb_auth':
						$id = 56;
						$name = 'cimb_authorization';
					break;
					case 'cimb_ipg':
						$id = 34;
						$name = 'cimb_ipg';
					break;
					case 'danamon':
						$id = 45;
						$name = 'danamon';
					break;
					case 'mandiri':
						$id = 53;
						$name = 'mandiri';
					break;
					case 'maybank':
						$id = 43;
						$name = 'maybank';
					break;
					case 'union_pay':
						$id = 54;
						$name = 'union_pay';
					break;
					case 'uob':
						$id = 46;
						$name = 'UOB';
					break;
					default:
						$id = 8;
						$name = 'BCA';
					break;
				}
			/* e-wallet */ 
			case 'ewallet':
				switch (strtolower($paymentId[1])) {
					case 'shopeepay':
						$id = 75;
						$name = 'shopeepay';
					break;
					case 'ovo':
						$id = 63;
						$name = 'ovo';
					break;
					default:
						$id = 13;
						$name = 'linkaja';
					break;
				}
			break;
			
		}
		return (object) ['id' => $id, 'name' => $name];
	}



	public function Callback(object $request)
	{
		// Example incoming data
		/*
			{
				"MerchantCode"	: "ID01625",
				"PaymentId"		: "30",
				"RefNo"		: "0007609625",
				"Amount"		: "250000",
				"Currency"		: "IDR",
				"Remark"		: "",
				"TransId"		: "T0051206200",
				"AuthCode"		: "",    
				"Status"		: "6",
				"ErrDesc"		: "",
				"Signature"		: "lkvo2Xuy7BImSfMoTBznJSUOEC8=",
				"CheckoutURL"	: "https://sandbox.ipay88.co.id/epayment/entry.asp?CheckoutID=5F822C024A102470C16A762C19EA29D7879A47B2EFF7C4151E309F00EDEADC6F&Signature=Nv2ub5JULwXf1X2x7B9CLe3z7K4%3d",
				"xfield1"		: ""
			}
		*/

		try {
			
			SELF::Validate($request, ['RefNo', 'Status', 'Amount']);
			$input = $this->init->getSecret().$this->init->getMID().$request->RefNo.$request->Amount.$request->Currency;
			$signature = base64_encode(hex2bin(sha1($input)));

			if (strcmp($signature, $request->Signature) === 0) {
			
				$content = [
						'status' => '000',
						'data' => (array) $request,
				];

				if ($content['data']['Status'] == 6) {
						$status = 'pending';
				}
				if ($content['data']['Status'] == 0) {
				  	$status = 'fail';
				}
				
				$result = [
						'request' => (array) $request,
						'response' => [
								'content' => json_encode($content),
								'status_code' => 200,
								// 'va_number' => $va_number,
								'bank_code' => $content['data']['PaymentId'],
								'amount' => $content['data']['Amount'],
								'transaction_id' => $content['data']['TransId'], // vendor transaction_id
								'order_id' => $content['data']['RefNo'], // PGA order_id
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