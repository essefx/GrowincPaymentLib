<?php

namespace Growinc\Payment;

use Growinc\Payment\Exceptions\InvalidArgumentException;

trait Helper
{

	/**
	 *
	 * Clean URL
	 *
	 */
	public static function CleanURL(string $url) : string
	{
		return preg_replace('/([^:])(\/{2,})/', '$1/', $url);
	}

	/**
	 *
	 * Debug show file & line
	 *
	 */
	public static function ThrowError($e) : string
	{
		if ($e instanceof \Exception) {
			return implode(':', [
				$e->getMessage(),
				// basename($e->getFile()),
				'/' . basename(dirname($e->getFile())) .
				'/' . basename($e->getFile()),
				$e->getLine()]
			);
		}
		return $e;
	}

	/**
	 *
	 * Return response
	 *
	 */
	public static function JSONResult($request, $response, $status_code = 200) : array
	{
		return [
			'request' => (array) $request,
			'response' => [
				'content' => json_encode($response),
				'status_code' => $status_code,
			],
		];
	}

	/**
	 *
	 * Debug show file & line as JSON
	 *
	 */
	public static function JSONError($e, $status_code = 200) : array
	{
		return [
			'response' => [
				'content' => json_encode([
					'status' => $e->getCode(),
					'error_message' => $e->getMessage(),
					'error_file' =>
						'/' . basename(dirname($e->getFile())) .
						'/' . basename($e->getFile()),
					'error_line' => $e->getLine(),
				]),
				'status_code' => $status_code,
			]
		];
	}

	/**
	 *
	 * Check if is JSON
	 *
	 */
	public static function is_JSON($string)
	{
		return is_string($string) && is_array(json_decode($string, true)) ? true : false;
	}

	/**
	 *
	 * Lazy ob to arr
	 *
	 */
	public static function ObjectToArray(object $object) : array
	{
		return json_decode(json_encode($object), true);
	}

	/**
	 *
	 * Argument validator
	 *
	 */
	public static function Validate(object $object, array $array)
	{
		foreach ($array as $a) {
			if (!isset($object->{$a}) || empty($object->{$a})) {
				throw new InvalidArgumentException('Missing argument ' . $a);
			}
		}
	}

}
