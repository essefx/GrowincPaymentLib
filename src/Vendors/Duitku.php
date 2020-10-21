<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Duitku extends Requestor implements VendorInterface
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
			$this->form['order_id'] = $this->transaction->getOrderID();
			$this->form['invoice_no'] = $this->transaction->getInvoiceNo();
			$this->form['amount'] = $this->transaction->getAmount();
			$this->form['description'] = $this->transaction->getDescription();
			$this->form['currency'] = $this->transaction->getCurrency();
			//
			$this->form['customer_name'] = $this->transaction->getCustomerName();
			$this->form['customer_email'] = $this->transaction->getCustomerEmail();
			$this->form['customer_phone'] = $this->transaction->getCustomerPhone();
			$this->form['customer_address'] = $this->transaction->getCustomerAddress();
			$this->form['country_code'] = $this->transaction->getCountryCode();
			//
			$this->form['billing_address'] = [
					'firstName' => $this->form['customer_name'],
					'lastName' => '',
					'address' => $this->form['customer_address'],
					'city' => '',
					'postalCode' => '',
					'phone' => $this->form['customer_phone'],
					'countryCode' => $this->form['country_code'],
				];
			$this->form['shipping_address'] = [
					'firstName' => $this->form['customer_name'],
					'lastName' => '',
					'address' => $this->form['customer_address'],
					'city' => '',
					'postalCode' => '',
					'phone' => $this->form['customer_phone'],
					'countryCode' => $this->form['country_code'],
				];
			$this->form['customer_details'] = [
					'firstName' => $this->form['customer_name'],
					'lastName' => '',
					'email' => $this->form['customer_email'],
					'phoneNumber' => $this->form['customer_phone'],
					'billingAddress' => $this->form['billing_address'],
					'shippingAddress' => $this->form['shipping_address'],
				];
			// VC	Credit Card (Visa / Master)
			// BK	BCA KlikPay
			// M1	Mandiri Virtual Account
			// BT	Permata Bank Virtual Account
			// A1	ATM Bersama
			// B1	CIMB Niaga Virtual Account
			// I1	BNI Virtual Account
			// VA	Maybank Virtual Account
			// FT	Ritel
			// OV	OVO
			// DN	Indodana Paylater
			// SP	Shopee Pay
			// SA	Shopee Pay Apps
			// AG	Bank Artha Graha
			// S1	Bank Sahabat Sampoerna
			$this->form['payment_method'] = $this->transaction->getPaymentMethod();
			$this->form['payment_url'] = $this->init->getPaymentURL() . '/v2/inquiry';
			$this->form['callback_url'] = $this->init->getCallbackURL();
			// Redirect
			// merchantOrderId: Nomor transaksi dari merchant abcde12345
			// reference: Nomor referensi transaksi dari Duitku. Mohon disimpan untuk keperluan pencatatan atau pelacakan transaksi. d011111
			// resultCode: Hasil status transaksi
			// 00 - Success
			// 01 - Pending
			// 02 - Canceled
			$this->form['return_url'] = $this->init->getReturnURL();
			// Request argseter
			$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
			$this->form['signature'] = md5(
					$this->init->getMID() .
					$this->form['order_id'] .
					(float) $this->form['amount'] .
					$this->init->getSecret()
				);
			// Go
			$this->request['form'] = $this->form;
			$this->request['time'] = $this->transaction->getTime();
			$this->request['url'] = $this->form['payment_url'];
			$this->request['data'] = [
					'merchantCode' => $this->init->getMID(),
					'paymentAmount' => $this->form['amount'],
					'paymentMethod' => $this->form['payment_method'],
					'merchantOrderId' => $this->form['order_id'],
					'productDetails' => $this->form['description'],
					'additionalargs' => '', // optional
					'merchantUserInfo' => '', // optional
					'customerVaName' => $this->form['customer_name'],
					'email' => $this->form['customer_email'],
					'phoneNumber' => $this->form['customer_phone'],
					'itemDetails' => [],
					'customerDetail' => $this->form['customer_details'],
					'callbackUrl' => $this->form['callback_url'],
					'returnUrl' => $this->form['return_url'],
					'signature' => $this->form['signature'],
					'expiryPeriod' => $this->form['expiry_period'],
				];
			$this->request['headers'] = [
					'Content-Type' => 'application/json',
					'Content-Length' => strlen(json_encode($this->request['data'])),
				];
			$post = $this->DoRequest('POST', $this->request);
			$result = $post;
			// Success
			/*
			{
				"merchantCode": "D6677",
				"reference": "D66774XLQY7KUDPP0WGA",
				"paymentUrl": "http://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=B1Q3XQMO6CY44R7F2",
				"vaNumber": "11990140616361",
				"amount": "100000",
				"statusCode": "00",
				"statusMessage": "SUCCESS"
			}
			*/
		} catch (\Throwable $e) {
			throw new \Exception(__FUNCTION__ . ' failed', 1);
		}
		return $result ?? [];
	}

	public function Callback(object $request)
	{
		// Example incoming data
		/*
		{
			"merchantCode": "D6677",
			"amount": "100000",
			"merchantOrderId": "0001285662",
			"productDetail": "Payment for order 0001285662",
			"additionalParam": null,
			"resultCode": "00",
			"signature": "439030a6da086ee13558137f07d4a27d",
			"paymentCode": "VC",
			"merchantUserId": null,
			"reference": "D6677JXVYL752HMAV0AD"
		}
		*/
		try {
			SELF::Validate($request, ['amount', 'merchantOrderId']);
			$signature = md5(
					$this->init->getMID() .
					(float) $request->amount .
					$request->merchantOrderId .
					$this->init->getSecret()
				);
			if (strcmp($signature, ($request->signature ?? '')) === 0) {
				/*
				$result = [
						'response' => [
								'status' => '000',
								'message' => 'Success',
								'data' => (array) $request,
							],
					];
				*/
				$result = [
						'response' => [
								'content' => json_encode($request),
								'status_code' => 200,
							],
					];
			} else {
				throw new \Exception(__FUNCTION__ . " signature check failed", 1);
			}
		} catch (\Throwable $e) {
			throw new \Exception(__FUNCTION__ . ' failed', 1);
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
			SELF::Validate($request, ['order_id']);
			$signature = md5(
					$this->init->getMID() .
					$request->order_id .
					$this->init->getSecret()
				);
			// Go
			$this->request['time'] = time();
			$this->request['url'] = $this->init->getRequestURL() . '/transactionStatus';
			$this->request['data'] = [
					'merchantCode' => $this->init->getMID(),
					'merchantOrderId' => $request->order_id,
					'signature' => $signature,
				];
			$this->request['headers'] = [
					'Content-Type' => 'application/json',
					'Content-Length' => strlen(json_encode($this->request['data'])),
				];
			$post = $this->DoRequest('POST', $this->request);
			$response = (array) $post['response'];
			extract($response);
			if (!empty($status_code) && $status_code === 200) {
				$content = (object) json_decode($content);
				if (	!empty($content->statusMessage)
						&& $content->statusMessage == "SUCCESS"
						&& $content->merchantOrderId == $request->order_id
				) {
					// Success
					/*
					{
						"merchantOrderId": "0001297441",
						"reference": "D6677KW403DFH8VOFMRJ",
						"amount": "100000",
						"fee": "0.00",
						"statusCode": "00",
						"statusMessage": "SUCCESS"
					}
					*/
					/*
					$data = [
							'status' => '000',
							'data' => [
									'response_code' => $content->statusCode,
									'response_message' => $content->statusMessage,
									//
									'order_id' => $content->merchantOrderId,
									'merchant_id' => $this->init->getMID(),
									'reference_id' => $content->reference,
									'amount' => (float) $content->amount,
									'fee' => (float) $content->fee,
									'_content' => $content,
								],
						];
					*/
					$result = [
							'response' => [
									// 'content' => json_encode($data),
									'content' => json_encode($content),
									'status_code' => 200,
									'headers' => $headers,
								],
						];
				} else {
					throw new \Exception($content->statusMessage, 1);
				}
			} else {
				throw new \Exception($content, 1);
			}
		} catch (\Throwable $e) {
			throw new \Exception(__FUNCTION__ . ' failed', 1);
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