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
	// protected $member_id;
	protected $Y_N;
	// protected $mode;
	// protected $is_paymen_notif;
	// protected $payment_id;
    protected $credential_attr;
    protected $req_datetime;
    protected $transaction_remak;
    
    protected $product_code;
    protected $promo_code;
    protected $is_async;
    protected $branch_id;
    protected $transaction_id;

	public function __construct()
	{
		$this->time = time();
		$this->order_id = '00' . substr($this->time, 2, strlen($this->time));
		$this->invoice_no = 'INV' . substr($this->time, 2, strlen($this->time));
		$this->description = 'Payment for ' . $this->invoice_no;
		$this->currency = 'IDR';
		$this->currency_code = '360';
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

	public function setUpdateOrderId(string $Y_N): void
	{
		$this->Y_N = $Y_N;
	}

	public function getUpdateOrderId(): ?string
	{
		return $this->Y_N;
	}

	public function setIsPaymentNotif(string $is_paymen_notif): void
	{
		$this->is_paymen_notif = $is_paymen_notif;
	}

	public function getIsPaymentNotif(): ?string
	{
		return $this->is_paymen_notif;
	}

	// // credit card end

	// public function setPaymentId(int $payment_id): void
	// {
	// 	$this->payment_id = $payment_id;
	// }

	// public function getPaymentId(): ?int
	// {
	// 	return $this->payment_id;
	// }

    public function setTransactionRemak(string $transaction_remak) : void
    {
        $this->transaction_remak = $transaction_remak;
    }
    public function getTransactionRemak() : ? string
    {
        return $this->transaction_remak;
    }

    public function setTransactionID(string $transaction_id) : void
    {
        $this->transaction_id = $transaction_id;
    }
    public function getTransactionID() : ? string
    {
        return $this->transaction_id;
    }

    public function setProductCode(string $product_code) : void
    {
        $this->product_code = $product_code;
    }
    public function getProductCode() : ? string
    {
        return $this->product_code;
    }

    public function setPromoCode(string $promo_code) : void
    {
        $this->promo_code = $promo_code;
    }
    public function getPromoCode() : ? string
    {
        return $this->promo_code;
    }

    public function setIsAsync(int $is_async) : void
    {
        $this->is_async = $is_async;
    }
    public function getIsAsync() : ? int
    {
        return $this->is_async;
    }

    public function setBranchId(string $branch_id) : void
    {
        $this->branch_id = $branch_id;
    }
    public function getBranchId() : ? string
    {
        return $this->branch_id;
    }

    public function setPostId(string $post_id) : void
    {
        $this->post_id = $post_id;
    }
    public function getPostId() : ? string
    {
        return $this->post_id;
    }

    public function setCredentialAttr(string $credential_attr): void
	{
		$this->credential_attr = $credential_attr;
	}
	public function getCredentialAttr(): ?string
	{
		return $this->credential_attr;
    }
    
    public function setReqDateTime(string $req_datetime): void
	{
		$this->req_datetime = $req_datetime;
	}
	public function getReqDateTime(): ?string
	{
		return $this->req_datetime;
	}


}
