<?php

namespace Growinc\Payment;

class Transaction extends TransactionExtends
{

	protected $time;
	//
	protected $order_id;
	protected $invoice_no;
	protected $amount;
	protected $description;
	protected $currency;
	protected $currency_code;
	//
	protected $customer_name;
	protected $customer_email;
	protected $customer_phone;
	protected $customer_address;
	protected $customer_city;
	protected $country_code;
	protected $postal_code;
	//
	protected $payment_method;
	protected $payment_type;
	protected $expire_at;
	//
	protected $item;
	//
	protected $customer_userid; // internet banking login
	// cc
	protected $cc_card_holder_name;
	protected $cc_card_number;
	protected $cc_card_exp_month;
	protected $cc_card_exp_year;
	protected $cc_card_cvv;
	protected $cc_token;
	// more
	protected $params;

	public function __construct()
	{
		$this->time = time();
		$this->order_id = '00' . substr($this->time, 2, strlen($this->time));
		$this->invoice_no = 'INV' . substr($this->time, 2, strlen($this->time));
		$this->description = 'Payment for ' . $this->invoice_no;
		$this->currency = 'IDR';
		$this->currency_code = '360';
		$this->expire_at = 2; // In hour
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

	public function setParams(array $params): void
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
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

	public function setCurrencyCode(string $currency_code): void
	{
		$this->currency_code = $currency_code;
	}

	public function getCurrencyCode(): ?string
	{
		return $this->currency_code;
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

	public function setCustomerCity($customer_city): void
	{
		$this->customer_city = $customer_city;
	}

	public function getCustomerCity(): ?string
	{
		return $this->customer_city;
	}

	public function setCountrycode($country_code): void
	{
		$this->country_code = $country_code;
	}

	public function getCountryCode(): ?string
	{
		return $this->country_code;
	}

	public function setPostalCode($postal_code): void
	{
		$this->postal_code = $postal_code;
	}

	public function getPostalCode(): ?string
	{
		return $this->postal_code;
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

	public function setPaymentType($payment_type): void
	{
		$this->payment_type = $payment_type;
	}

	public function getPaymentType(): ?string
	{
		return $this->payment_type;
	}

	public function setExpireAt($expire_at): void
	{
		$this->expire_at = $expire_at;
	}

	public function getExpireAt(): ?string
	{
		return $this->expire_at;
	}

	//

	// public function setItem(&$item_detail): void
	public function setItem($item): void
	{
		$this->item = $item;
	}

	// public function getItem(): ?array
	public function getItem()
	{
		return $this->item;
	}

	// bca_klikbca

	public function setCustomerUserID(string $customer_userid): void
	{
		$this->customer_userid = $customer_userid;
	}

	public function getCustomerUserID(): ?string
	{
		return $this->customer_userid;
	}

	// credit card start

	public function setCardHolderName(string $cc_card_holder_name): void
	{
		$this->cc_card_holder_name = $cc_card_holder_name;
	}

	public function getCardHolderName(): ?string
	{
		return $this->cc_card_holder_name;
	}

	public function setCardNumber(string $cc_card_number): void
	{
		$this->cc_card_number = $cc_card_number;
	}

	public function getCardNumber(): ?string
	{
		return $this->cc_card_number;
	}

	public function setCardExpMonth(string $cc_card_exp_month): void
	{
		$this->cc_card_exp_month = $cc_card_exp_month;
	}

	public function getCardExpMonth(): ?string
	{
		return $this->cc_card_exp_month;
	}

	public function setCardExpYear(string $cc_card_exp_year): void
	{
		$this->cc_card_exp_year = $cc_card_exp_year;
	}

	public function getCardExpYear(): ?string
	{
		return $this->cc_card_exp_year;
	}

	public function setCardCVV(string $cc_card_cvv): void
	{
		$this->cc_card_cvv = $cc_card_cvv;
	}

	public function getCardCVV(): ?string
	{
		return $this->cc_card_cvv;
	}

	public function setCardToken(string $cc_token): void
	{
		$this->cc_token = $cc_token;
	}

	public function getCardToken(): ?string
	{
		return $this->cc_token;
	}

}
