<?php

namespace Growinc\Payment\Vendors;

// use Growinc\Payment\Init;
use Growinc\Payment\Requestor;
// use Growinc\Payment\Transaction;
use Growinc\Payment\Vendors\VendorInterface;

class Duitku extends Requestor implements VendorInterface
{

	protected $form;

	// public function __construct(Setup $setup)
	// {
	// 	$this->setup = $setup;
	// 	$this->form = (object) [];
	// }

	public function Index()
	{
		// $get = $this->Get($this->setup, $this->setup->payment_url, []);
		// print_r($get);
	}

	public function GetToken($param)
	{

	}

	public function CreateDummyForm($param)
	{

	}

	public function RedirectPayment()
	{
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
		// Request parameter
		$this->form['expiry_period'] = $this->transaction->getExpireAt(); // minutes
		// Go
		$this->form['signature'] = md5(
				$this->init->getMID() .
				$this->form['order_id'] .
				(float) $this->form['amount'] .
				$this->init->getSecret()
			);
		$this->form['time'] = $this->transaction->getTime();
		$this->form['url'] = $this->form['payment_url'];
		$this->form['data'] = [
				'merchantCode' => $this->init->getMID(),
				'paymentAmount' => $this->form['amount'],
				'paymentMethod' => $this->form['payment_method'],
				'merchantOrderId' => $this->form['order_id'],
				'productDetails' => $this->form['description'],
				'additionalParam' => '', // optional
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
		$this->form['headers'] = [
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->form['data'])),
			];
		// print_r($this->form);
		$post = $this->Request('POST', $this->form);
		extract($post);
		$response = [
				'content' => $response->getBody()->getContents(),
				'status_code' => $response->getStatusCode(),
				// 'headers' => $response->getHeaders(),
			];
		print_r($response);
	}

	public function SecurePayment($param)
	{

	}

	public function Callback($param)
	{

	}

	public function CallbackAlt($param)
	{

	}

	public function Inquiry($param)
	{

	}

	public function Cancel($param)
	{

	}

	public function Settle($param)
	{

	}

	public function Refund($param)
	{

	}

	public function RefundStatus($param)
	{

	}

}