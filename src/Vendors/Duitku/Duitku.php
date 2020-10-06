<?php

namespace Growinc\Payment\Vendors;

use Growinc\Payment\Blueprint;
use Growinc\Payment\Client;
use Growinc\Payment\Transaction;

class Duitku extends Transaction implements Blueprint
{

	private $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
		// print_r($this->client);
	}

	public function Index()
	{
		$get = $this->Get($this->client, $this->client->payment_url);
		print_r($get);
	}

	public function GetToken($args)
	{
	}

	public function RedirectPaymentCreateForm($args)
	{
	}

	public function RedirectPayment($args)
	{
	}

	public function RedirectPaymentCallback($args)
	{
	}

	public function RedirectPaymentCallbackAlt($args)
	{
	}

	public function SecurePaymentCreateForm($args)
	{
	}

	public function SecurePayment($args)
	{
	}

	public function SecurePaymentCallback($args)
	{
	}

	public function SecurePaymentCallbackAlt($args)
	{
	}

	public function PaymentOptionInquiry($args)
	{
	}

	public function PaymentOptionCancel($args)
	{
	}

	public function PaymentOptionSettle($args)
	{
	}

	public function PaymentOptionrefund($args)
	{
	}

	public function PaymentOptionRefundStatus($args)
	{
	}


}