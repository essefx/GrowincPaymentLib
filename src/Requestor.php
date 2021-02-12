<?php

namespace Growinc\Payment;

class Requestor
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

	public function getResponse(): ?object // PSR7 stream object

	{
		return $this->response;
	}

	//

	public function DoRequest(string $method, $request)
	{
		try {
			$response = SELF::_GetClient([
					'base_uri' => $this->init->getBaseURI(),
				])->sendRequest(
					$method,
					$request['url'] ?? '',
					$request['data'] ?? [],
					$request['headers'] ?? [],
					$request['option'] ?? [],
				);
			if (is_array($response)) {
				$return = [
						'request' => $request,
						//   'request' => [
						//       'time' => $request['time'] ?? time(),
						//       'url' => $request['url'] ?? '',
						//       'data' => $request['data'] ?? [],
						//       'data_raw' => $request['data_raw'] ?? [],
						//       'headers' => $request['headers'] ?? [],
						//   ],
						'response' => $response,
					];
			} elseif (is_object($response)) {
				$return = [
						'request' => $request,
						//   'request' => [
						//       'time' => $request['time'] ?? time(),
						//       'url' => $request['url'] ?? '',
						//       'data' => $request['data'] ?? [],
						//       'data_raw' => $request['data_raw'] ?? [],
						//       'headers' => $request['headers'] ?? [],
						//   ],
						'response' => [
								'content' => $response->getBody()->getContents(),
								'status_code' => (int) $response->getStatusCode(),
								'headers' => $response->getHeaders(),
							],
					];
				$response->getBody()->rewind();
			} elseif (is_string($response)) {
				$return = $response;
			}
		} catch (\Throwable $e) {
			throw new \Exception($this->ThrowError($e));
		}
		$this->request = $request;
		$this->response = $response;
		return $return ?? null;
	}
}
