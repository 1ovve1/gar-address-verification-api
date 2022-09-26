<?php declare(strict_types=1);

namespace DB\Exceptions\Unchecked;

use JetBrains\PhpStorm\Pure;
use RuntimeException;
use Throwable;

class DriverImplementationNotFoundException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "driver implementation not found by: \n\tDBType: '%s'\n\tAdditional: '%s'";

	/**
	 * @inheritDoc
	 */
	public function __construct(string $dbType, string $additional, ?Throwable $prev = null)
	{
		parent::__construct(sprintf(
			self::MESSAGE_TEMPLATE,
			$dbType, $additional
		), previous: $prev);
	}


}