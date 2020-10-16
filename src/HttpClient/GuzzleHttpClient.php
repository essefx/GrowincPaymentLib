<?php

namespace Growinc\Payment\HttpClient;

// use Exception;
use GuzzleHttp\Client as GuzzleClient;
// use GuzzleHttp\ClientInterface;
// use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
// use GuzzleHttp\Exception\ServerException;
// use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
// use Growinc\Payment\Exceptions\ApiException;

// use Growinc\Payment\Setup;

class GuzzleHttpClient
{

	protected $_guzzle_http;
	private static $_guzzle_instance;

	public function __construct()
	{
		if (!$this->_guzzle_http) {
			$this->_guzzle_http = new GuzzleClient([
					// 'base_uri' => $setup->base_uri,
					'verify' => false,
					'timeout' => 60,
				]);
		}
		// if (!$this->_client) {
		// 	$this->_client = new GuzzleClient([
		// 			// 'base_uri' => $this->,
		// 			'verify' => false,
		// 			'timeout' => 60,
		// 		]);
		// }
	}

	public static function getInstance()
	{
		if (!SELF::$_guzzle_instance) {
			SELF::$_guzzle_instance = new SELF();
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
						'status_code' => $response->getStatusCode(),
						'headers' => $response->getHeaders(),
					];
			}
			// $response = new Psr7\Response($res->getStatusCode(), $res->getHeaders(), $res->getBody()->getContents());
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				$response = [
						'content' => $e->getResponse()->getBody()->getContents(),
						'status_code' => $e->getResponse()->getStatusCode(),
						'headers' => $e->getResponse()->getHeaders(),
					];
			}
		}
		return $response;
	}

	/*
	public function _sendRequest($method, string $url, $data, array $headers)
	{

	// public function Request($param)
	// {
		// print_r($param);
		// exit();
		// ob_start();
		// extract($param);
		// $request['time'] = $time ?? time();
		// $request['method'] = $method;
		// $request['url'] = $url;
		// $request['headers'] = $headers;
		// $request['data'] = $data;
		// $response = [];

		// try {
		// 	$ch = curl_init();
		// 	curl_setopt($ch, CURLOPT_URL, $url);
		// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		// 	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// 	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		// 			'Content-Type: application/json',
		// 			'Content-Length: ' . strlen(json_encode($data)),
		// 		]);
		// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// 	$response = curl_exec($ch);
		// 	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// 	if ($status_code == 200) {
		// 		//
		// 	}
		// } catch (Exception $e) {
		// 	http_response_code(404);
		// 	//throw $th;
		// }
		// print_r($response);
		// exit();
		// ob_end_flush();


		try {
			$res = $this->_client->request('POST', $url, [
					'headers' => [$headers],
					'form_params' => $data,
				]);


			// $raw =  [
			// 		'headers' => $request['headers'],
			// 		(strtoupper($request['method']) === 'GET' ? 'query' : 'form_params') => $request['data'],
			// 		\GuzzleHttp\RequestOptions::JSON => $request['data'],
			// 		// 'http_errors ' => true,
			// 	];
			// print_r($raw);
			// exit();
			// switch (strtoupper($request['method'])) {
			// 	case 'GET':
					// $res = $this->_client->request(
					// 		$request['method'],
					// 		$request['url'],
					// 		$raw
					// 	);
			// 		break;
			// 	case 'POST':
			// 		$res = $this->_client->request($request['method'], $request['url'], [
			// 				'headers' => $request['headers'],
			// 				'form_params' => $request['data'],
			// 				'http_errors ' => true,
			// 			]);
			// 		break;
			// }
			$response = [
					'content' => $res->getBody()->getContents(),
					'headers' => $res->getHeaders(),
					'status_code' => $res->getStatusCode(),
				];
			$response = new Psr7\Response($res->getStatusCode(), $res->getHeaders(), $res->getBody()->getContents());
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				$response = [
						'content' => $e->getResponse()->getBody()->getContents(),
						'headers' => $e->getResponse()->getHeaders(),
						'status_code' => $e->getResponse()->getStatusCode(),
					];
				// $response = '';
				// $body = $e->getResponse()->getBody()->getContents();
				// $response = $body->getContents();
				// print_r(strlen($body));
				// exit();
				// while (!$body->eof()) {
				// 	$response .= $body->read(strlen($body));
				// }
			}
			// $response = [
			// 		'content' => $e->getMessage(),
			// 		// 'status_code' => $res->getStatusCode(),
			// 	];
			// throw new ApiException($e->getMessage());

			// throw new \Exception(SELF::_ShowError($e), 1);
		// } catch (ServerException $e) {
			// throw new \Exception($response, 1);
			// echo Psr7\str($e->getRequest());
			// if ($e->hasResponse()) {
			// 	echo Psr7\str($e->getResponse());
			// }
		}
		// echo $response['content'];
		return $response;
		// return ['request' => $request ?? [], 'response' => $response ?? []];
	}

	// public static function _ShowError($e)
	// {
	// 	// Debug show file & line
	// 	return implode(':', [$e->getMessage(), basename($e->getFile()), $e->getLine()]);
	// }
	*/

}
