<?php declare(strict_types=1);

namespace GAR\Exceptions\Checked;

use Exception;

class AddressNotFoundException extends Exception
{
	const MESSAGE_TEMPLATE = "Address not found: %s";

	public function __construct(?string $userAddress = null)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $userAddress ?? "input incorrect or address not exists");

		parent::__construct(
			$message,
			8
		);
	}
}