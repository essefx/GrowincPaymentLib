<?php

namespace Growinc\Payment\Exceptions;

interface ExceptionInterface extends \Throwable
{

	public function getErrorCode();
	public function getErrorMessage();

}
