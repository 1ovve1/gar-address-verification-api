<?php declare(strict_types=1);

namespace GAR\Exceptions\Unchecked;

use GAR\Storage\Codes;
use RuntimeException;

class CodeTypeNotFoundException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "Code '%s' type not found, use these types: '%s'";

	/**
	 * @param string $actualType
	 * @param array<Codes> $codeTypes
	 */
	public function __construct(string $actualType, array $codeTypes)
	{
		/** @var array<string> $codeTypesList */
		$codeTypesList = [];
		foreach ($codeTypes as $codeType) {
			$codeTypesList[] = $codeType->value;
		}

		$message = sprintf(self::MESSAGE_TEMPLATE, $actualType, implode(', ', $codeTypesList));

		parent::__construct($message, 64);
	}


}