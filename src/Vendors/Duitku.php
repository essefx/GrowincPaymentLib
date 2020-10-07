<?php
namespace Growinc\Payment\Vendors;

use Growinc\Payment\Setup;
use Growinc\Payment\Transaction;
use Growinc\Payment\Vendors\VendorInterface;

class Duitku extends Transaction implements VendorInterface
{

	protected $setup;
	protected $payment;

	public function __construct(Setup $setup)
	{
		$this->setup = $setup;
	}

	public function Index()
	{
		$get = $this->Get($this->setup, $this->setup->payment_url, []);
		print_r($get);
	}

	public function GetToken($param)
	{
	}

	public function CreateDummyForm($param)
	{
	}

	public function RedirectPayment($param)
	{
		extract($param);
		// Format
		$this->payment->time = time();
		$this->payment->order_id = $order_id ?? ('00' . substr($this->payment->time, 2, strlen($this->payment->time)));
		$this->payment->invoice_no = $invoice_no ?? ('INV' . substr($this->payment->time, 2, strlen($this->payment->time)));
		$this->payment->amount = $amount ?? 0;
		$this->payment->currency = $currency ?? 'IDR';
		//
		$this->payment->customer_name = $customer_name ?? '';
		$this->payment->customer_email = $customer_email ?? '';
		$this->payment->customer_phone = $customer_phone ?? '';
		$this->payment->customer_address = $customer_address ?? '';
		$this->payment->country_code = $country_code ?? 'ID';
		$this->payment->billing_address = $billing_address ?? [
				'firstName' => $this->payment->customer_name,
				'lastName' => '',
				'address' => $this->payment->customer_address,
				'city' => '',
				'postalCode' => '',
				'phone' => $this->payment->customer_phone,
				'countryCode' => $this->payment->country_code,
			];
		$this->payment->shipping_address = $shipping_address ?? [
				'firstName' => $this->payment->customer_name,
				'lastName' => '',
				'address' => $this->payment->customer_address,
				'city' => '',
				'postalCode' => '',
				'phone' => $this->payment->customer_phone,
				'countryCode' => $this->payment->country_code,
			];
		$this->payment->customer_details = [
				'firstName' => $this->payment->customer_name,
				'lastName' => '',
				'email' => $this->payment->customer_email,
				'phoneNumber' => $this->payment->customer_phone,
				'billingAddress' => $this->payment->billing_address,
				'shippingAddress' => $this->payment->shipping_address,
			];
		//
		$this->payment->payment_description = $description ?? ('Payment for order ' . $this->payment->order_id);
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
		$this->payment->payment_method = $payment_method ?? 'VC';
		$this->payment->payment_url = $this->setup->payment_url . '/v2/inquiry'; // 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'
		$this->payment->callback_url = $callback_url;
		// Redirect
		// merchantOrderId: Nomor transaksi dari merchant abcde12345
		// reference: Nomor referensi transaksi dari Duitku. Mohon disimpan untuk keperluan pencatatan atau pelacakan transaksi. d011111
		// resultCode: Hasil status transaksi
		// 00 - Success
		// 01 - Pending
		// 02 - Canceled
		$this->payment->return_url = $return_url;
		// Request parameter
		$this->payment->expiry_period = 100; // minutes
		// Go
		$signature = md5(
				$this->setup->mid .
				$this->payment->order_id .
				(float) $this->payment->amount .
				$this->setup->secret
			);
		$data = [
				'merchantCode' => $this->setup->mid,
				'paymentAmount' => $this->payment->amount,
				'paymentMethod' => $this->payment->payment_method,
				'merchantOrderId' => $this->payment->order_id,
				'productDetails' => $this->payment->payment_description,
				'additionalParam' => '', // optional
				'merchantUserInfo' => '', // optional
				'customerVaName' => $this->payment->customer_name,
				'email' => $this->payment->customer_email,
				'phoneNumber' => $this->payment->customer_phone,
				'itemDetails' => [],
				'customerDetail' => $this->payment->customer_details,
				'callbackUrl' => $this->payment->callback_url,
				'returnUrl' => $this->payment->return_url,
				'signature' => $signature,
				'expiryPeriod' => $this->payment->expiry_period,
			];
		$headers = [
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($data)),
			];
		$post = $this->Post(
				$this->setup,
				$this->payment->payment_url,
				$data,
				$headers
			);
		// extract($post[1]);
		print_r($post);
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