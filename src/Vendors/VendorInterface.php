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
	public function Callback(object $request);
	public function CallbackAlt(object $request);
	//
	public function Inquiry(object $request);
	public function Cancel(object $request);
	public function Settle(object $request);
	public function Refund(object $request);
	public function RefundStatus(object $request);

}
