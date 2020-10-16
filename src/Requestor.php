<?php

namespace Growinc\Payment;

use Growinc\Payment\Init;
use Growinc\Payment\Transaction;
use Growinc\Payment\HttpClient\GuzzleHttpClient;

class	Requestor
{

	protected $init;
	protected $transaction;
	//
	private static $_client;
	//
	protected $request;
	protected $response;

	public function __construct(Init $init, Transaction $transaction)
	{
		$this->init = $init;
		$this->transaction = $transaction;
	}

	private static function _GetClient()
	{
		if (!SELF::$_client) {
			SELF::$_client = GuzzleHttpClient::getInstance();
		}
		return SELF::$_client;
	}

	public function Request(string $method, $request)
	{
		// $this->request['time'] = $this->transaction->getTime();
		// $this->request['method'] = $method;
		// $this->request['url'] = $url;
		// $this->request['data'] = $data;
		// $this->request['headers'] = $headers;
		// $guzzle = new GuzzleHttpClient();
		$this->request = $request;
		$this->response = SELF::_GetClient()->sendRequest(
				// $this->request['method'],
				// $this->request['url'],
				// $this->request['data'],
				// $this->request['headers'],
				$method,
				$this->request['url'],
				$this->request['data'],
				$this->request['headers'],
			);
		return [
				'request' => [
						'time' => $this->request['time'],
						'url' => $this->request['url'],
						'data' => $this->request['data'],
						'headers' => $this->request['headers'],
					],
				'response' => $this->response
			];
		//->getBody()->getContents();
	}

	// public function Get(Setup $setup, $url, $headers = [])
	// {
	// 	$this->request['time'] = time();
	// 	$this->request['method'] = 'GET';
	// 	$this->request['url'] = $url;
	// 	$this->request['headers'] = $headers;
	// 	$guzzle = new GuzzleHttpClient($setup);
	// 	$this->response = $guzzle->Request($this->request);
	// 	return $this->response;
	// }

}
