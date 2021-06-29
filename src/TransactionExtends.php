<?php

namespace Growinc\Payment;

class TransactionExtends
{

/* */
	protected $seller;
	protected $is_paymen_notif;

	protected $ruuid;
	protected $Y_N;
	protected $transaction_remark;

	protected $transaction_id;
	protected $product_code;
	protected $promo_code;
	protected $is_async;
	protected $branch_id;

	protected $post_id;
	protected $credential_attr;
	protected $req_datetime;

	public function __construct()
	{
		// espay attr
		// $this->signature_key = 'ces0bu1jh9qrsakq';
		// $this->password = 'Y0F,(5EM=#';
		// $this->comm_code = 'SGWGROWINC';
		// $this->mode = [
		// 	'sendinvoice' => 'SENDINVOICE',
		// 	'checkstatus' => 'CHECKSTATUS',
		// 	'closeinvoice' => 'CLOSEDINVOICE'
		// ];
	}

	//

	public function setSeller(&$seller_detail): void
	{
		$this->seller = $seller_detail;
	}

	// public function getSeller(): ?array
	// {
	// 	return $this->seller;
	// }

	public function setIsPaymentNotif(string $is_paymen_notif): void
	{
		$this->is_paymen_notif = $is_paymen_notif;
	}

	public function getIsPaymentNotif(): ?string
	{
		return $this->is_paymen_notif;
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

	public function setTransactionRemark(string $transaction_remark): void
	{
		$this->transaction_remark = $transaction_remark;
	}

	public function getTransactionRemark(): ?string
	{
		return $this->transaction_remark;
	}

	public function setTransactionID(string $transaction_id): void
	{
		$this->transaction_id = $transaction_id;
	}

	public function getTransactionID(): ?string
	{
		return $this->transaction_id;
	}

	public function setProductCode(string $product_code): void
	{
		$this->product_code = $product_code;
	}

	public function getProductCode(): ?string
	{
		return $this->product_code;
	}

	public function setPromoCode(string $promo_code): void
	{
		$this->promo_code = $promo_code;
	}
	public function getPromoCode(): ?string
	{
		return $this->promo_code;
	}

	public function setIsAsync(int $is_async): void
	{
		$this->is_async = $is_async;
	}

	public function getIsAsync(): ?int
	{
		return $this->is_async;
	}

	public function setBranchId(string $branch_id): void
	{
		$this->branch_id = $branch_id;
	}

	public function getBranchId(): ?string
	{
		return $this->branch_id;
	}

	//

	public function setPostId(string $post_id): void
	{
		$this->post_id = $post_id;
	}

	public function getPostId(): ?string
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
/* */

}
