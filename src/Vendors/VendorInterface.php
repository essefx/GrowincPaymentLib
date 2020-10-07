<?php
namespace Growinc\Payment\Vendors;

interface VendorInterface
{
	public function Index();
	public function GetToken($param);
	//
	public function CreateDummyForm($param);
	public function RedirectPayment($param);
	//
	public function SecurePayment($param);
	public function Callback($param);
	public function CallbackAlt($param);
	//
	public function Inquiry($param);
	public function Cancel($param);
	public function Settle($param);
	public function Refund($param);
	public function RefundStatus($param);
}
