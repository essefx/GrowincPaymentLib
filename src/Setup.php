<?php
namespace Growinc\Payment;

class	Setup
{

	public $mid;
	public $secret;
	public $token;
	//
	public $base_uri;
	public $payment_url;
	public $callback_url;
	public $return_url;

	public function __construct($mid, $secret)
	{
		$this->mid = $mid;
		$this->secret = $secret;
	}

	public function setURL($param)
	{
		extract($param);
		$this->base_uri = $base_uri ?? '';
		$this->payment_url = $payment_url ?? '';
		$this->callback_url = $callback_url ?? '';
		$this->return_url = $return_url ?? '';
	}

	public function getURL()
	{
		return $this->url;
	}


}