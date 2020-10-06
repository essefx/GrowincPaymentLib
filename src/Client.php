<?php

namespace Growinc\Payment;

class	Client
{

	public $mid;
	public $secret;
	public $token;
	//
	public $payment_url;
	public $callback_url;
	public $return_url;

	public function __construct($mid, $secret)
	{
		$this->mid = $mid;
		$this->secret = $secret;
	}

	public function setURL($payment_url, $callback_url, $return_url)
	{
		// $this->url = [
		// 		'$payment_url' => $payment_url,
		// 		'$callback_url' => $callback_url,
		// 		'$return_url' => $return_url,
		// 	];
		$this->payment_url = $payment_url;
		$this->callback_url = $callback_url;
		$this->return_url = $return_url;
	}

	public function getURL()
	{
		return $this->url;
	}


}