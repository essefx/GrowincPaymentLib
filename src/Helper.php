<?php

namespace Growinc\Payment;

use InvalidArgumentException;

trait Helper
{

	/**
	 *
	 * Debug show file & line
	 *
	 */
	public static function ShowError($e) : string
	{
		return implode(':', [$e->getMessage(), basename($e->getFile()), $e->getLine()]);
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
