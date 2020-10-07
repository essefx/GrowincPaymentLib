<?php
namespace Growinc\Payment\Exceptions;

class ApiException extends \Exception
{

	protected $error_code;

	public function getErrorCode()
	{
		return $this->error_code;
	}

	public function __construct($message, $code = null, $error_code = null)
	{
		if (!$message) {
			throw new $this('Unknown ' . get_class($this));
		}
		parent::__construct($message, $code);
		$this->error_code = $error_code;
	}

}
