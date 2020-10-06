<?php

namespace Growinc\Payment;

use GuzzleHttp\Client as Guzzle;

use Growinc\Payment\Client;

class	Transaction
{

	private $base_url;
	private $query_url;
	//
	private $time;
	private $request;
	private $response;

	public function Post(Client $client, $url, $data, $headers = [])
	{
		$this->request['time'] = time();
		// Request
		// $this->request['mid'] = $client->mid;
		// $this->request['secret'] = $client->secret;
		// $this->request['token'] = $client->token;
		//
		$this->request['url'] = $url;
		$this->request['headers'] = $headers ?? [
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			];
		$this->request['data'] = $data;
		// Response
		// $this->response = Curl::to($this->request['url'])
		// 	->withHeader($this->request['headers'])
		// 	->withOption('SSL_VERIFYHOST', '0')
		// 	->withOption('SSLVERSION', '6')
		// 	->withOption('SSL_VERIFYPEER', false)
		// 	->withTimeout(60)
		// 	->returnResponseObject()
		// 	->withData($this->request['data'])
		// 	->asJson()
		// 	->post();
		$this->post = (new Guzzle)->request('POST', $this->request['url'], [
					'verify' => false,
					'headers' => $this->request['headers'],
					'form_params' => $this->request['data'],
				]);
		$this->response = [
				'content' => (string) $this->post->getBody(),
				'status_code' => (string) $this->post->getStatusCode(),
			];
		return [$this->request, $this->response];
	}

	public function Get(Client $client, $url, $headers = [])
	{
		$this->request['time'] = time();
		// Request
		// $this->request['mid'] = $client->mid;
		// $this->request['secret'] = $client->secret;
		// $this->request['token'] = $client->token;
		//
		$this->request['url'] = $url;
		$this->request['headers'] = $headers ?? [
				'Content-Type' => 'application/json',
				'Content-Length' => strlen(json_encode($this->request['data'])),
			];
		// Response
		// $this->response = Curl::to($this->request['url'])
		// 	->withHeader($this->request['headers'])
		// 	->withOption('SSL_VERIFYHOST', '0')
		// 	->withOption('SSLVERSION', '6')
		// 	->withOption('SSL_VERIFYPEER', false)
		// 	->withTimeout(60)
		// 	->returnResponseObject()
		// 	->get();
		$this->get = (new Guzzle)->request('GET', $this->request['url'], [
					'verify' => false,
					'headers' => $this->request['headers'],
				]);
		$this->response = [
				'content' => (string) $this->get->getBody(),
				'status_code' => (string) $this->get->getStatusCode(),
			];
		return [$this->request, $this->response];
	}

}