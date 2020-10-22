<?php

namespace Growinc\Payment;

class	Requestor
{

	use Helper;

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

	//

	private static function _GetClient($args)
	{
		if (!SELF::$_client) {
			SELF::$_client = \Growinc\Payment\HttpClient\GuzzleHttpClient::getInstance($args);
		}
		return SELF::$_client;
	}

	//

	public function getRequest(): ?object
	{
		return (object) $this->request;
	}

	public function getResponse(): ?object // PSR7 object
	{
		return $this->response;
	}

	//

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
					($this->request['headers'] ?? []),
					($this->request['option'] ?? []),
				);
			$result = [
					'request' => [
							'time' => $this->request['time'],
							'url' => $this->request['url'],
							'data' => $this->request['data'],
							'headers' => $this->request['headers'],
						],
					'response' => [
							'content' => $this->response->getBody()->getContents(),
							'status_code' => (int) $this->response->getStatusCode(),
							'headers' => $this->response->getHeaders(),
						],
				];
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		return $result;
	}

}
