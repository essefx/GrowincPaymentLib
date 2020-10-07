<?php
namespace Growinc\Payment;

use Growinc\Payment\HttpClient\GuzzleHttpClient;
use Growinc\Payment\Setup;

class	Transaction
{

	public $request;
	public $response;

	public function Post(Setup $setup, $url, $data, $headers = [])
	{
		$this->request['time'] = time();
		$this->request['method'] = 'POST';
		$this->request['url'] = $url;
		$this->request['headers'] = $headers;
		$this->request['data'] = $data;
		$guzzle = new GuzzleHttpClient($setup);
		$this->response = $guzzle->Request($this->request);
		return [$this->request, $this->response];
	}

	public function Get(Setup $setup, $url, $headers = [])
	{
		$this->request['time'] = time();
		$this->request['method'] = 'GET';
		$this->request['url'] = $url;
		$this->request['headers'] = $headers;
		$guzzle = new GuzzleHttpClient($setup);
		$this->response = $guzzle->Request($this->request);
		return [$this->request, $this->response];
	}

}