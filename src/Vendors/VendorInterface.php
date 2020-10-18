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
	public function Callback($request);
	public function CallbackAlt($request);
	//
	public function Inquiry($request);
	public function Cancel($request);
	public function Settle($request);
	public function Refund($request);
	public function RefundStatus($request);

}
