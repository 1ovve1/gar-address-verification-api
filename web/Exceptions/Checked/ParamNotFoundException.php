<?php declare(strict_types=1);

namespace GAR\Exceptions\Checked;

use Exception;

class ParamNotFoundException extends Exception
{
	const MESSAGE_TEMPLATE = "Param '%s' was not found" . PHP_EOL;

	/**
	 * @param string $paramName
	 * @param array<mixed> $actualDataStructure
	 * @param string $additional
	 */
	public function __construct(string $paramName, array $actualDataStructure, string $additional = '')
	{
		if (!empty($additional)) {
			$additional = 'Additional: ' . $additional . PHP_EOL;
		}

		$message = sprintf(
		self::MESSAGE_TEMPLATE . $additional,
			$paramName,
		);

		parent::__construct(
			$message,
			1
		);
	}

}