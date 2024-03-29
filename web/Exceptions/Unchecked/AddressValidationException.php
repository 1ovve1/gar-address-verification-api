<?php declare(strict_types=1);

namespace GAR\Exceptions\Unchecked;

use RuntimeException;

class AddressValidationException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "Error while parse address because %s";

	public function __construct(string $message)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $message);

		parent::__construct($message, 32);
	}


}