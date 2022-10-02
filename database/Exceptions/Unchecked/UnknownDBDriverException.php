<?php declare(strict_types=1);

namespace DB\Exceptions\Unchecked;

use RuntimeException;

class UnknownDBDriverException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "DB Driver not found by:\n\tDB Type: %s\n\tDriver source: %s\n\tMessage: %s";

	/**
	 * @param string|null $dbType
	 * @param array<mixed> $driverSource
	 * @param string $message
	 */
	function __construct(?string $dbType, array $driverSource = [], string $message = '')
	{

		$message = sprintf(
			self::MESSAGE_TEMPLATE,
			$dbType ?? "undefined db type in _ENV['DB_TYPE']",
			print_r($driverSource, true),
			$message
		);
		parent::__construct($message);
	}
}