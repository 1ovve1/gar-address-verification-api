<?php declare(strict_types=1);

namespace DB\Exceptions\Checked;

use Exception;

class ConditionNotFoundException extends Exception
{
	const MESSAGE_TEMPLATE = "Condition '%s' was not found in current driver (DBType: '%s')";

	public function __construct(
		public readonly string $dbType,
		public readonly string $input
	)
	{
		parent::__construct(sprintf(
			self::MESSAGE_TEMPLATE,
			$input, $dbType
		));
	}

}