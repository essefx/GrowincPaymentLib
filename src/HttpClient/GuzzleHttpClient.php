<?php

namespace Growinc\Payment\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Growinc\Payment\Exceptions\ApiException;

class GuzzleHttpClient
{

	protected $_guzzle_http;
	private static $_guzzle_instance;

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

	private static function _HandleError($response)
	{
		$content = $response['content'];
		$status_code = $response['status_code'];
		$message = $content['message'] ?? $content;
		$error_code = $content['error_code'] ?? '0';
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

	public function sendRequest(string $method, string $url, $data, array $headers)
	{
		try {
			$response = $this->_guzzle_http->request((string) $method, (string) $url, [
					'headers' => [(array) $headers],
					(strtoupper($method) === 'GET' ? 'query' : 'form_params') => (array) $data,
				]);
			if (isset($data['option']['json']) && $data['option']['json'] == '1') {
				$response = [
						'content' => $response->getBody()->getContents(),
						'status_code' => (int) $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					];
			} else {
				// Default is return as PSR7 response
			}
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				$response = [
						'content' => $e->getResponse()->getBody()->getContents(),
						'status_code' => (int) $e->getResponse()->getStatusCode(),
						'headers' => $e->getResponse()->getHeaders(),
					];
			}
			if (!isset($skip_error)) {
				SELF::_HandleError($response);
			}
		}
		return $response;
	}

}
