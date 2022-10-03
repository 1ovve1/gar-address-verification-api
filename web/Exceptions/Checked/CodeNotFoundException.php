<?php declare(strict_types=1);

namespace GAR\Exceptions\Checked;

use Exception;

class CodeNotFoundException extends Exception
{
	const MESSAGE_TEMPLATE = "Code not found: %s";

	public function __construct(?int $objectId = null)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $objectId ?? "input incorrect, address not exists or objectid not exist");

		parent::__construct(
			$message,
			4
		);
	}

}