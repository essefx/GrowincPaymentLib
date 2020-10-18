<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Requestor;
use Growinc\Payment\Vendors\VendorInterface;

class Xendit extends Requestor implements VendorInterface
{

	protected $form;

	public function Index()
	{
		// Inapplicable
	}

	public function GetToken($args)
	{
		// Inapplicable
	}

	public function CreateDummyForm($args)
	{
		// Inapplicable
	}

	public function RedirectPayment(\Growinc\Payment\Transaction $transaction)
	{
		// Inapplicable
	}

	public function SecurePayment(\Growinc\Payment\Transaction $transaction)
	{
		// Inapplicable
	}

	public function Callback($request)
	{
		// Inapplicable
	}

	public function CallbackAlt($request)
	{
		// Inapplicable
	}

	public function Inquiry($request)
	{
		// Inapplicable
	}

	public function Cancel($request)
	{
		// Inapplicable
	}

	public function Settle($request)
	{
		// Inapplicable
	}

	public function Refund($request)
	{
		// Inapplicable
	}

	public function RefundStatus($request)
	{
		// Inapplicable
	}

}