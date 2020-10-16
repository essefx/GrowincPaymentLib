<?php

namespace Growinc\Payment;

class	Requestor
{

	protected $init;
	protected $transaction;
	//
	private static $_client;
	//
	protected $request;
	protected $response;

	public function __construct(\Growinc\Payment\Init $init)
	{
		$this->init = $init;
	}

	private static function _GetClient($args)
	{
		if (!SELF::$_client) {
			SELF::$_client = \Growinc\Payment\HttpClient\GuzzleHttpClient::getInstance($args);
		}
		return SELF::$_client;
	}

	public function DoRequest(string $method, $request)
	{
		try {
			$this->request = $request;
			$this->response = SELF::_GetClient([
					'base_uri' => $this->init->getBaseURI(),
				])->sendRequest(
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
					// 'response' => $this->response // return as PSR7 resposne
					'response' => [
							'content' => $this->response->getBody()->getContents(),
							'status_code' => $this->response->getStatusCode(),
							'headers' => $this->response->getHeaders(),
						],
				];
		} catch (\Throwable $e) {
			throw new \Exception(SELF::_ShowError($e), 1);
		}
	}

	private static function _ShowError($e)
	{
		// Debug show file & line
		return implode(':', [$e->getMessage(), basename($e->getFile()), $e->getLine()]);
	}

}
