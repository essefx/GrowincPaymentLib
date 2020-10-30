<?php

namespace Growinc\Payment\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Exception\RequestException;

use Growinc\Payment\Exceptions\ApiException;

class GuzzleHttpClient
{

	protected $_guzzle_http;
	private static $_guzzle_instance;
	//
	protected $effective_uri;

	public function __construct($args)
	{
		if (!$this->_guzzle_http) {
			$this->_guzzle_http = new GuzzleClient(array_merge([
					'verify' => false,
					'timeout' => 60,
				], $args));
		}
	}

	//

	private static function _HandleAPIError($e)
	{
		$message = trim($e->getMessage());
		$status_code = (int) $e->getResponse()->getStatusCode();
		$error_code = $e->getCode();
		throw new ApiException($message, $status_code, $error_code);
	}

	//

	public static function getInstance($args)
	{
		if (!SELF::$_guzzle_instance) {
			SELF::$_guzzle_instance = new SELF($args);
		}
		return SELF::$_guzzle_instance;
	}

	public function sendRequest(string $method, string $url, $data, array $headers = [], array $option = [])
	{
		try {
			if(strtoupper($method) === 'GET') {
				$type = 'query';
			} else {
				$type = 'form_params';
				if(isset($option['request_opt']) && !empty($option['request_opt'])){
					$type = $option['request_opt'];
				}
			}
			
			$response = $this->_guzzle_http->request((string) $method, (string) $url, [
					'headers' => (array) $headers, 
					$type => (array) $data,
					'on_stats' => function(TransferStats $stats) {
							$this->effective_uri = $stats->getEffectiveUri();
						}
				]);
			if (isset($option['to_json']) && !empty($option['to_json'])) {
				$response = [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					];
			} elseif (isset($option['to_uri']) && !empty($option['to_uri'])) {
				$response = (string) strval($this->effective_uri);
			} else {
				// Default is return as PSR7 response
			}
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				SELF::_HandleAPIError($e);
			}
		}
		return $response;
	}
	
}
