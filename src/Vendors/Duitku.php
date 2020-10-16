<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Duitku extends Requestor implements VendorInterface
{

	protected $form;

	public function Index()
	{

	}

	public function GetToken($args)
	{

	}

	public function CreateDummyForm($args)
	{

	}

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
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
		$this->form['headers'] = [
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->form['data'])),
			];
		$post = $this->DoRequest('POST', $this->form);
		return $post;
	}

	public function SecurePayment($args)
	{

	}

	public function Callback($args)
	{

	}

	public function CallbackAlt($args)
	{

	}

	public function Inquiry($args)
	{

	}

	public function Cancel($args)
	{

	}

	public function Settle($args)
	{

	}

	public function Refund($args)
	{

	}

	public function RefundStatus($args)
	{

	}

}