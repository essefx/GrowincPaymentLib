<?php

namespace Growinc\Payment\Exceptions;

class ApiException extends \Exception implements \Growinc\Payment\Exceptions\ExceptionInterface
{

	protected $error_code;
	protected $error_message;

	public function getErrorCode()
	{
		return $this->error_code;
	}

	public function getErrorMessage()
	{
		return $this->error_message;
	}

	public function __construct($message, $code = null, $error_code = null)
	{
		if (!$message) {
			throw new $this('Unknown ' . get_class($this));
		}
		parent::__construct($message, $code);
		$this->error_code = $error_code;
		$this->error_message = $message;
	}

}
