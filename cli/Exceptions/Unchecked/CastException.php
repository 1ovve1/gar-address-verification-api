<?php declare(strict_types=1);

namespace CLI\Exceptions\Unchecked;

use RuntimeException;

class CastException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "Incorrect type: " . PHP_EOL . "Given type '%s' for cast attribute value '%s'" . PHP_EOL;


	public function __construct(string $castType, mixed $value)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $castType, print_r($value, true));

		parent::__construct($message);
	}

}