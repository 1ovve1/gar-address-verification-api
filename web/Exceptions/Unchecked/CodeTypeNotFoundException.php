<?php declare(strict_types=1);

namespace GAR\Exceptions\Unchecked;

use GAR\Storage\Codes;
use RuntimeException;

class CodeTypeNotFoundException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "Code '%s' type not found, use these types: '%s'";

	/**
	 * @param mixed $actualType
	 * @param array<Codes> $codeTypes
	 */
	public function __construct(mixed $actualType, array $codeTypes)
	{
		/** @var array<string> $codeTypesList */
		$codeTypesList = [];
		foreach ($codeTypes as $codeType) {
			$codeTypesList[] = $codeType->value;
		}

		$message = sprintf(
			self::MESSAGE_TEMPLATE,
			print_r($actualType, true),
			implode(', ', $codeTypesList)
		);

		parent::__construct($message, 64);
	}


}