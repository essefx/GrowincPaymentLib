<?php

namespace Growinc\Payment\Vendors;

interface VendorInterface
{

	public function Index();
	public function GetToken($args);
	//
	public function CreateDummyForm($args);
	public function RedirectPayment(\Growinc\Payment\Transaction $transaction);
	//
	public function SecurePayment(\Growinc\Payment\Transaction $transaction);
	public function Callback($args);
	public function CallbackAlt($args);
	//
	public function Inquiry($args);
	public function Cancel($args);
	public function Settle($args);
	public function Refund($args);
	public function RefundStatus($args);

}
