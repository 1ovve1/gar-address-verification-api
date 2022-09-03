<?php declare(strict_types=1);

namespace GAR\Exceptions;

use Exception;

class CodeNotFoundException extends Exception
{
	const MESSAGE_TEMPLATE = "Code not found by these param: %d (objectid)";

	public function __construct(int $objectId)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $objectId);

		parent::__construct(
			$message,
			4,
			null
		);
	}

}