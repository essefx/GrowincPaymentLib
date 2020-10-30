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
	//
	protected $payment_type;
	protected $item;
	protected $customer_userid;
	// cc
	protected $cc_card_number;
	protected $cc_card_exp_month;
	protected $cc_card_exp_year;
	protected $cc_card_cvv;
	
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
	
	public function setPaymentType($payment_type): void
	{
		$this->payment_type = $payment_type;
	}

	public function getPaymentType(): ?string
	{
		return $this->payment_type;
	}
	
	public function setItem(&$item_detail): void
	{
		$this->item = $item_detail;
	}
	
	public function getItem(): ?array
	{
		return $this->item;
	}
	// bca_klikbca
	public function setCustomerUserid(string $customer_userid): void
	{
		$this->customer_userid = $customer_userid;
	}

	public function getCustomerUserid(): ?string
	{
		return $this->customer_userid;
	}
	// credit card start
	public function setCardNumber(string $cc_card_number): void
	{
		$this->cc_card_number = $cc_card_number;
	}
	public function getCardNumber(): ?string
	{
		return $this->cc_card_number;
	}
	public function setCardExpMonth(int $cc_card_exp_month): void
	{
		$this->cc_card_exp_month = $cc_card_exp_month;
	}
	public function getCardExpMonth(): ?int
	{
		return $this->cc_card_exp_month;
	}
	public function setCardExpYear(int $cc_card_exp_year): void
	{
		$this->cc_card_exp_year = $cc_card_exp_year;
	}
	public function gettCardExpYear(): ?int
	{
		return $this->cc_card_exp_year;
	}
	public function setCardExpCvv(int $cc_card_cvv): void
	{
		$this->cc_card_cvv = $cc_card_cvv;
	}
	public function getCardExpCvv(): ?int
	{
		return $this->cc_card_cvv;
	}
	// credit card end
}
