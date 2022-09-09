<?php declare(strict_types=1);

namespace GAR\Exceptions\Checked;

use Exception;

class CodeNotFoundException extends Exception
{
	const MESSAGE_TEMPLATE = "Code not found by these param: %s (objectid)";

	public function __construct(?int $objectId = null)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $objectId ?? "param not found");

		parent::__construct(
			$message,
			4
		);
	}

}