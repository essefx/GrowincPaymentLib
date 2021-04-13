<?php

namespace Growinc\Payment;

use Growinc\Payment\Exceptions\InvalidArgumentException;

trait Helper
{

	/**
	 *
	 * Debug show file & line
	 *
	 */
	public static function ThrowError($e) : string
	{
		if ($e instanceof \Exception) {
			return implode(':', [$e->getMessage(), basename($e->getFile()), $e->getLine()]);
		}
		return $e;
	}

	/**
	 *
	 * Debug show file & line as JSON
	 *
	 */
	public static function JSONError($e) : string
	{
		return json_encode([
				'content' => json_encode([
						'error_message' => $e->getMessage(),
						'error_file' => $e->getFile(),
						'error_line' => $e->getLine(),
					]),
				'status_code' => (int) $e->getResponse()->getStatusCode(),
				'headers' => $e->getResponse()->getHeaders(),
			]);
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
