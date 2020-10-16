<?php

namespace Growinc\Payment;

class	Transaction
{

	protected $time;
	//
	protected $order_id;
	protected $invoice_no;
	protected $amount;
	protected $description;
	protected $currency;
	//
	protected $customer_name;
	protected $customer_email;
	protected $customer_phone;
	protected $customer_address;
	protected $country_code;
	//
	protected $payment_method;
	protected $expire_at;

	public function __construct()
	{
		$this->time = time();
		$this->order_id = '00' . substr($this->time, 2, strlen($this->time));
		$this->invoice_no = 'INV' . substr($this->time, 2, strlen($this->time));
		$this->description = 'Payment for ' . $this->invoice_no;
		$this->currency = 'IDR';
		$this->expire_at = 100;
	}

	public function setTime(string $time): void
	{
		$this->time = $time;
	}

	public function getTime(): ?string
	{
		return $this->time;
	}

	//

	public function setOrderID(string $order_id): void
	{
		$this->order_id = $order_id;
	}

	public function getOrderID(): ?string
	{
		return $this->order_id;
	}

	public function setInvoiceNo(string $invoice_no): void
	{
		$this->invoice_no = $invoice_no;
	}

	public function getInvoiceNo(): ?string
	{
		return $this->invoice_no;
	}

	public function setAmount(int $amount): void
	{
		$this->amount = $amount;
	}

	public function getAmount(): ?int
	{
		return $this->amount;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setCurrency(string $currency): void
	{
		$this->currency = $currency;
	}

	public function getCurrency(): ?string
	{
		return $this->currency;
	}

	//

	public function setCustomerName(string $customer_name): void
	{
		$this->customer_name = $customer_name;
	}

	public function getCustomerName(): ?string
	{
		return $this->customer_name;
	}

	public function setCustomerEmail(string $customer_email): void
	{
		$this->customer_email = $customer_email;
	}

	public function getCustomerEmail(): ?string
	{
		return $this->customer_email;
	}

	public function setCustomerPhone($customer_phone): void
	{
		$this->customer_phone = $customer_phone;
	}

	public function getCustomerPhone(): ?string
	{
		return $this->customer_phone;
	}

	public function setCustomerAddress($customer_address): void
	{
		$this->customer_address = $customer_address;
	}

	public function getCustomerAddress(): ?string
	{
		return $this->customer_address;
	}

	public function setCountrycode($country_code): void
	{
		$this->country_code = $country_code;
	}

	public function getCountryCode(): ?string
	{
		return $this->country_code;
	}

	//

	public function setPaymentMethod($payment_method): void
	{
		$this->payment_method = $payment_method;
	}

	public function getPaymentMethod(): ?string
	{
		return $this->payment_method;
	}

	public function setExpireAt($expire_at): void
	{
		$this->expire_at = $expire_at;
	}

	public function getExpireAt(): ?string
	{
		return $this->expire_at;
	}
}
