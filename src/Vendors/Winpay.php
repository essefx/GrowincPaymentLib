<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;
use Growinc\Payment\Transaction;

class Winpay extends Requestor implements VendorInterface
{
  protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		try{
			$this->request['headers'] = [
				'Content-Type' => 'application/json',
		        'Accept' => 'application/json',
		        'Authorization' => 'Basic '.base64_encode($args['api_key1'].':'.$args['api_key2']),
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
      		if(!empty($response->rc) && $response->rc === '00'){
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
		try{
			
			$this->transaction = $transaction;
			//
			$this->form['order_id'] =  $this->transaction->getOrderID();
			
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

			// $this->form['payment_url'] = $this->init->getPaymentURL();
			// $this->form['currency'] = $this->transaction->getCurrency();


      		$this->form['billing_address'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => 'IPSUM',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => $this->form['customer_address'],
					'city' => $this->form['city'] ?? 'Jakarta',
					'postal_code' => $this->form['postal_code'],
					'country_code' => $this->form['country_code'] ?? 'IDN',
				];
			$this->form['shipping_address'] = [
					'first_name' => $this->form['customer_name'],
					'last_name' => 'IPSUM',
					'email' => $this->form['customer_email'],
					'phone' => $this->form['customer_phone'],
					'address' => $this->form['customer_address'],
					'city' => $this->form['city'] ?? 'Jakarta',
					'postal_code' => $this->form['postal_code'],
					'country_code' => $this->form['country_code'] ?? 'IDN',
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
			$this->form['payment_url'] = $this->init->getPaymentURL();
		

			// set amount
			$amountTotal = 0;
			foreach($this->form['item_details'] as $price){
			$amountTotal += (int) $price['unitPrice'] * (int) $price['qty'] ;
			}
		
		
			$this->form['token'] = $this->init->getMID().$this->init->getSecret();
			// $this->form['payment_date'] = date('Ymdhis');
			$this->form['get_link'] = 'no';


			$paymentMethod =  explode(',', $this->transaction->getPaymentMethod());

			$bankName = $this->getpaymentName($paymentMethod);

			// echo $bankName->name;exit();

			$this->form['payment_method'] = $paymentMethod[1];
			$this->form['payment_type'] =  $paymentMethod[0];
			$this->form['get_token_url'] = $this->init->getPaymentURL() . '/token';
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes


			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			

			// create spi_signature 
			$merchant_key = $this->init->getMerchantKey();
			$spi_token =  $this->init->getMID().$this->init->getSecret();
			$spi_merchant_transaction_reff = $this->transaction->getOrderID();
			$spi_amount =  $amountTotal;
			$spi_amount = number_format(doubleval($spi_amount),2,".","");
			$spi_signature = strtoupper(sha1( $spi_token . '|' . $merchant_key . '|' . $spi_merchant_transaction_reff . '|' . $spi_amount . '|0|0' ));

			$this->form['signature'] =  $spi_signature;
    
			// $this->request['headers'] = [
				// 	'Content-Type' => 'application/json',
				// 	'Accept' => 'application/json',
				// 	'Authorization' => 'Basic '.base64_encode($this->init->getMID().':'.$this->init->getSecret()),
				// 	'Content-Length' => strlen(json_encode($this->request['data'])),
				// ];

			// $get_token = $this->DoRequest('POST', $this->request);

			$this->form['create_token'] = [
			'api_key1' => $this->init->getMID(),
			'api_key2' => $this->init->getSecret(),
			'token_url' => $this->form['get_token_url']
			];

			$getToken = $this->GetToken($this->form['create_token']);

			$this->form['get_token'] = $getToken->token;

			// $this->form['payment_url'] = $this->init->getPaymentURL() .'/apiv2/'.$paymentMethod[1];
			$this->form['payment_url'] = $this->init->getPaymentURL() .'/apiv2/'.$bankName->name;
			$this->request['url'] = $this->form['payment_url'];
			$this->form['xpayment_date'] = date( "YmdHis", strtotime(date('H:i:s'))+(60*$this->form['expiry_period']));

			$addArray = [];
			switch ($bankName->name) {
				case 'QRISPAY':
					$addArray = [
						"spi_qr_type" => "static",
						"spi_qr_fee_type" => "percent",
						"spi_qr_fee" => "10",
					];
				break;

			}

			$dataPost = [
			      'cms' => "WINPAY API",
			      'spi_callback' => $this->init->getCallbackURL(),
			      'url_listener' => $this->init->getReturnURL(),
			      'spi_currency' => $this->form['currency'],
			      'spi_item' => $this->form['item_details'],
			      'spi_amount' => $amountTotal,
			      'spi_signature' => $this->form['signature'],
			      'spi_token' => $this->form['token'],
			      'spi_merchant_transaction_reff' => $spi_merchant_transaction_reff,
			      'spi_billingPhone' => $this->form['customer_phone'],
								'spi_billingEmail' => $this->form['customer_email'],
			      'spi_billingName' => $this->form['customer_name'],
								'spi_paymentDate' => $this->form['xpayment_date'],
								'get_link'=> 'no',
								
			    ];
			$dataPost = $dataPost + $addArray;

			// print_r($dataPost);exit();

			$this->request['data'] = [
			  'token' => $this->form['get_token'],
			  'json_string' => json_encode(
			    $dataPost
			  )
			];
			// print_r($this->request);exit();
      		$messageEncrypted = $this->OpenSSLEncrypt($this->request['data']['json_string'], $this->request['data']['token']);
			// $this->request['data'] = substr($messageEncrypted, 0, 10). $this->request['data']['token']. substr($messageEncrypted, 10);
			$this->request['data'] = ['orderdata' => substr($messageEncrypted, 0, 10). $this->request['data']['token']. substr($messageEncrypted, 10)];

			$this->request['headers'] = [
				'Content-Type' => 'application/x-www-form-urlencoded',
			];

			// $this->request['option'] = [
			// 	'as_json' => false,
			// ];
    
			$post = $this->DoRequest('POST',  $this->request);
		
			$response = (array) $post['response'];
			// $response = json_decode($response['content']);
			// print_r($response);exit();
			extract($response);
		

			// if (!empty($response->rc) && $response->rc === '00') {
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (!empty($content->rc)
						&& $content->rc == '00'
				) {
					
					/* Success
					{
						"status_code": "201",
						"status_message": "Success, PERMATA VA transaction is successful",
						"transaction_id": "7ba0a676-24e5-4648-8b94-e2bf02888f8c",
						"order_id": "0003880223",
						"gross_amount": "100000.00",
						"currency": "IDR",
						"payment_type": "bank_transfer",
						"transaction_time": "2020-10-28 17:17:21",
						"transaction_status": "pending",
						"fraud_status": "accept",
						"permata_va_number": "530002269464288",
						"merchant_id": "G345053042"
					}
					*/

					$content = [
							'status' => '000',
							'data' => (array) $response,
					];

					// print_r($content['data']['data']);exit();
						
					$result = [
							'request' => (array) $this->request,
							'response' => [
								'content' => json_encode($content),
								'status_code' => 200,
								// 'va_number' => $content['data']['data']->payment_code,
								// 'bank_code' => $this->form['payment_method'],
								// 'amount' => $content['data']['data']->total_amount,
								// 'transaction_id' => $content['data']['data']->order_id, // vendor transaction id
								// 'order_id' => $content['data']['data']->order_id, // PGA order_id
								// 'payment_type' => $this->form['payment_type'],
								// 'transaction_status' => $content['data']['rc'],
							],

							// 'response' => [
							// 		'content' => json_encode($response),
							// 		'status_code' => 200,
							// 	],
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


	function OpenSSLEncrypt($message, $key)
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

	/**
	 * @param array
	 * @return object
	 * */ 
	
	public function getPayName($paymentId){
		// switch ($paymentId[0]) {
		// 	/* Bank Transfer */ 
		// 	case 'bank_transfer':
		// 		$name = strtoupper($paymentId[1]);
		// 	break;

		// 	/* Payment Code */ 
		// 	case 'payment_code':
		// 		$name = strtoupper($paymentId[1]);
		// 	break;
		// }
		// $name = strtoupper($paymentId[1]);
		$name = strtoupper($paymentId[1]);
		return (object) ['type' => $paymentId[0],  'name' => $name];
	}

	public function getpaymentName($paymentId)
	{
		switch (strtolower($paymentId[0])) {
			/* Bank Transfer */ 
			case 'bank_transfer':
				switch (strtolower($paymentId[1])) {
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
				switch (strtolower($paymentId[1])) {
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
				switch (strtolower($paymentId[1])) {
					case 'qris':
						$name = 'QRISPAY';
					break;
				}

			case 'pulsa':
				switch (strtolower($paymentId[1])) {
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
				switch (strtolower($paymentId[1])) {
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

	public function getBankValue($value)
	{
		$arrData = [
			'bca' => 'BCAVA',
			'bni'=> 'BNIVA',
			'bri' => 'BRIVA',
			'mandiri' => 'MANDIRIVA',
			'permata' => 'PERMATAVA',
		];
		$search = array_search($value, $arrData);
		if($search == '')
			$search = $value;

		return $search;
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
		
			$input = $request->no_reff.$request->id_transaksi;
			$get = $this->init->getSecret().$this->init->getMID();
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
				}else if($content['data']['response_code'] == 01){
					$status = 'access denied !';
				}
				else if($content['data']['response_code'] == 04){
					$status = 'data not found ';
				}
				else if($content['data']['response_code'] == 05){
					$status = 'General Error ';
				}
				else if($content['data']['response_code'] == 99){
					$status = 'Parameter not valid';
				}
				
				$result = [
						'request' => (array) $request,
						'response' => [
								'content' => json_encode($content),
								'status_code' => 200,
								'bank_code' => $this->getBankValue($content['data']['method_code']),
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