<?php

namespace Growinc\Payment;

class Transaction
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
	protected $country_code;
	//
	protected $payment_method;
	protected $expire_at;
	//
	protected $payment_type;
	protected $item;
	//
	protected $customer_userid; // klikbca login
	// cc
	protected $cc_card_number;
	protected $cc_card_exp_month;
	protected $cc_card_exp_year;
	protected $cc_card_cvv;
	protected $cc_token;
	//
	protected $ruuid;
	protected $member_id;
	protected $password;
	protected $signature;
	protected $key;
	protected $comm_code;
	protected $bank_code;
	protected $Y_N;
	protected $signature_key;
	protected $mode;
	protected $is_paymen_notif;
	protected $payment_id;

	public function __construct()
	{
		$this->time = time();
		$this->order_id = '00' . substr($this->time, 2, strlen($this->time));
		$this->invoice_no = 'INV' . substr($this->time, 2, strlen($this->time));
		$this->description = 'Payment for ' . $this->invoice_no;
		$this->currency = 'IDR';
		$this->currency_code = '360';
		$this->expire_at = 100;
		// espay attr
		$this->signature_key = 'ces0bu1jh9qrsakq';
		$this->password = 'Y0F,(5EM=#';
		$this->comm_code = 'SGWGROWINC';
		$this->mode = [
			'sendinvoice' => 'SENDINVOICE',
			'checkstatus' => 'CHECKSTATUS',
			'closeinvoice' => 'CLOSEDINVOICE'
		];
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

	//

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

	public function setCustomerUserID(string $customer_userid): void
	{
		$this->customer_userid = $customer_userid;
	}

	public function getCustomerUserID(): ?string
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

	public function getCardExpYear(): ?int
	{
		return $this->cc_card_exp_year;
	}

	public function setCardExpCVV(int $cc_card_cvv): void
	{
		$this->cc_card_cvv = $cc_card_cvv;
	}

	public function getCardExpCVV(): ?int
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

	//

	public function setRuuid(string $ruuid): void
	{
		$this->ruuid = $ruuid;
	}

	public function getRuuid(): ?string
	{
		return $this->ruuid;
	}

	public function setMemberid(string $member_id): void
	{
		$this->member_id = $member_id;
	}

	public function getMemberid(): ?string
	{
		return $this->member_id;
	}

	public function setCommcode(string $comm_code): void
	{
		$this->comm_code = $comm_code;
	}

	public function getCommcode(): ?string
	{
		return $this->comm_code;
	}

	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setSignature(string $signature): void
	{
		$this->signature = $signature;
	}

	public function getSignature(): ?string
	{
		return $this->signature;
	}

	public function setKey(string $key): void
	{
		$this->key = $key;
	}

	public function getKey(): ?string
	{
		return $this->key;
	}

	public function setBankCode(string $bank_code): void
	{
		$this->bank_code = $bank_code;
	}

	public function getBankCode(): ?string
	{
		return $this->bank_code;
	}

	public function setUpdateOrderId(string $Y_N): void
	{
		$this->Y_N = $Y_N;
	}

	public function getUpdateOrderId(): ?string
	{
		return $this->Y_N;
	}

	public function setSignatureKey(string $signature_key): void
	{
		$this->signature_key = $signature_key;
	}

	public function getSignatureKey(): ?string
	{
		return $this->signature_key;
	}

	public function setMode(array $mode): void
	{
		$this->mode = $mode;
	}

	public function getMode(): ?array
	{
		return $this->mode;
	}

	public function setIsPaymentNotif(string $is_paymen_notif): void
	{
		$this->is_paymen_notif = $is_paymen_notif;
	}

	public function getIsPaymentNotif(): ?string
	{
		return $this->is_paymen_notif;
	}

	// credit card end

	public function setPaymentId(int $payment_id): void
	{
		$this->payment_id = $payment_id;
	}

	public function getPaymentId(): ?int
	{
		return $this->payment_id;
	}

}
