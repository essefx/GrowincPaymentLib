<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Winpay extends Requestor implements VendorInterface
{
  protected $form;

	public function Index()
	{
		// Inapplicable
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
				if (!empty($content->rc) && $content->rc == '00'
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

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		try{

      $this->transaction = $transaction;
     
      $this->form['payment_url'] = $this->init->getPaymentURL();
      // $this->form['spi_callback'] = $this->init->getCallbackURL();
      // $this->form['url_listener'] = $this->init->getReturnURL();
      $this->form['currency'] = $this->transaction->getCurrency();
      $this->form['order_id'] =  $this->transaction->getOrderID();

      // 
      $this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
      $this->form['country_code'] = $this->transaction->getCountryCode();

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
      
      $this->form['item_details'] =  $this->transaction->getItem();

      // set amount
      $amountTotal = 0;
      foreach($this->form['item_details'] as $price){
        $amountTotal += (int) $price['unitPrice'] * (int) $price['qty'] ;
      }

      $this->form['amount'] = $amountTotal;

      $this->form['token'] = $this->init->getMID().$this->init->getSecret();
     
      // $this->form['payment_date'] = date('Ymdhis');
      $this->form['get_link'] = 'no';


      
      $this->form['payment_method'] = $this->transaction->getPaymentMethod();
      $this->form['payment_type'] = $this->transaction->getPaymentType();
			$this->form['get_token_url'] = $this->init->getPaymentURL() . '/token';
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
      

			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();


      // create spi_signature 
      $merchant_key = $this->init->getMerchantKey();
      $spi_token =  $this->init->getMID().$this->init->getSecret();
      $spi_merchant_transaction_reff = $this->transaction->getOrderID();
      $spi_amount = $this->form['amount'];
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

      $this->form['payment_url'] = $this->init->getPaymentURL() .'/apiv2/'.$this->transaction->getPaymentMethod();
      $this->request['url'] = $this->form['payment_url'];
			$this->form['xpayment_date'] = date( "YmdHis", strtotime(date('H:i:s'))+(60*$this->form['expiry_period']));

      $this->request['data'] = [
          'token' => $this->form['get_token'],
          'json_string' => json_encode(
            [
              'cms' => "WINPAY API",
              'spi_callback' => $this->init->getCallbackURL(),
              'url_listener' => $this->init->getReturnURL(),
              'spi_currency' => $this->form['currency'],
              'spi_item' => $this->form['item_details'],
              'spi_amount' => $this->form['amount'],
              'spi_signature' => $this->form['signature'],
              'spi_token' => $this->form['token'],
              'spi_merchant_transaction_reff' => $spi_merchant_transaction_reff,
              'spi_billingPhone' => $this->form['customer_phone'],
							'spi_billingEmail' => $this->form['customer_email'],
							'spi_paymentDate' => $this->form['xpayment_date'],
              'spi_billingName' => $this->form['customer_name']
            ]
          )
      ];

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
			$response = json_decode($response['content']);
			if (!empty($response->rc) && $response->rc === '00') {
				if (!empty($response->rc)
						&& $response->rc == '00'
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

					$result = [
							'request' => (array) $this->request,
							'response' => [
									'content' => json_encode($response),
									'status_code' => 200,
								],
						];
				} else {
					throw new \Exception($content->status_message);
				}
			} else {
				throw new \Exception($response);
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
			"status_code": "201",
			"status_message": "midtrans payment notification",
			"transaction_id": "6fd88567-62da-43ff-8fe6-5717e430ffc7",
			"order_id": "0003960969",
			"gross_amount": "150000.00",
			"payment_type": "bank_transfer",
			"transaction_time": "2016-06-19 18:23:21",
			"transaction_status": "settlement",
			"fraud_status": "accept",
			"permata_va_number": "8562000087926752",
			"signature_key": "b8d7baceab8967af2fdebb82f497fbf4be957e0147f34e910fe9abfc533f883f1206e6c7a72d111ff61331254e3ff9f609c16cc81762e15d9ee6c53de36c65ff"
		}
		*/
		try {
			SELF::Validate($request, ['order_id', 'status_code', 'gross_amount']);
			$input = $request->order_id.$request->status_code.$request->gross_amount.$this->init->getMID();
			$signature = openssl_digest($input, 'sha512');

			// print_r($signature);exit();

			if (strcmp($signature, $request->signature_key) === 0) {
				$content = [
						'status' => '000',
						'data' => (array) $request,
					];
				$result = [
						'request' => (array) $request,
						'response' => [
								'content' => json_encode($content),
								'status_code' => 200,
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