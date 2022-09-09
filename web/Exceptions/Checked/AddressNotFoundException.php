<?php declare(strict_types=1);

namespace GAR\Exceptions\Checked;

use Exception;

class AddressNotFoundException extends Exception
{
	const MESSAGE_TEMPLATE = "Code not found by these param: %s (userAddress)";

	public function __construct(?string $userAddress = null)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $userAddress ?? "param not found");

		parent::__construct(
			$message,
			8
		);
	}
}