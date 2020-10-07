<?php
namespace Growinc\Payment\HttpClient;

use GuzzleHttp\Client as GuzzleClient;
// use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
// use GuzzleHttp\Exception\ServerException;
// use GuzzleHttp\Psr7;
use Growinc\Payment\Exceptions\ApiException;

use Growinc\Payment\Setup;

class GuzzleHttpClient
{

	private $_client;

	public function __construct(Setup $setup)
	{
		$this->_client = new GuzzleClient([
				'base_uri' => $setup->base_uri,
				'verify' => false,
				'timeout' => 60,
			]);
	}

	public function Request($param)
	{
		extract($param);
		$request['time'] = $time ?? time();
		$request['method'] = $method;
		$request['url'] = $url;
		$request['headers'] = $headers ?? [];
		$request['data'] = $data ?? [];
		$response = [];
		try {
			// switch (strtoupper($request['method'])) {
			// 	case 'GET':
					$res = $this->_client->request($request['method'], $request['url'], [
							'headers' => $request['headers'],
							(strtoupper($request['method']) === 'GET' ? 'query' : 'form_params') => $request['data'],
							'http_errors ' => true,
						]);
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
		} catch (RequestException $e) {
			if ($e->hasResponse()) {
				// $response = '';
				$body = $e->getResponse()->getBody();
				$response = $body->getContents();
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
			// throw new \Exception(SELF::_ShowError($e), 1);
			// echo Psr7\str($e->getRequest());
			// if ($e->hasResponse()) {
			// 	echo Psr7\str($e->getResponse());
			// }
		}
		return [$request, $response];
	}

	public static function _ShowError($e)
	{
		// Debug show file & line
		return implode(':', [$e->getMessage(), basename($e->getFile()), $e->getLine()]);
	}

}
