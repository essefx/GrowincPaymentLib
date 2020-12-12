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
			switch (strtoupper(trim($method))) {
				case 'GET':
					$type = 'query';
					break;
				case 'POST':
					$type = 'form_params';
					break;
			}
			// Overide
			if (isset($option['as_json']) && !empty($option['as_json'])) {
				$type = 'json';
			}
			// Request options
			$option = array_merge($option, [
					'headers' => $headers,
					$type => $data,
				]);
			$on_stat = [
					'on_stats' => function(TransferStats $stats) {
							$this->effective_uri = $stats->getEffectiveUri();
						},
				];
			$response = $this->_guzzle_http->request(
					(string) $method,
					(string) $url,
					array_merge($option, $on_stat)
				);
			if (isset($option['to_json']) && !empty($option['to_json'])) {
				$return = [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					];
				$response->getBody()->rewind();
			} elseif (isset($option['to_uri']) && !empty($option['to_uri'])) {
				$return = (string) strval($this->effective_uri);
			} else {
				// Default is return as PSR7 response
				$return = $response;
			}
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				SELF::_HandleAPIError($e);
			}
		}
		return $return;
	}

}
