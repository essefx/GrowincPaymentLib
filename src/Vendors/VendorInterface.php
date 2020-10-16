<?php

namespace Growinc\Payment\Vendors;

interface VendorInterface
{

	public function Index();
	public function GetToken($args);
	//
	public function CreateDummyForm($args);
	public function RedirectPayment();
	//
	public function SecurePayment($args);
	public function Callback($args);
	public function CallbackAlt($args);
	//
	public function Inquiry($args);
	public function Cancel($args);
	public function Settle($args);
	public function Refund($args);
	public function RefundStatus($args);

}
