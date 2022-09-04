<?php

declare(strict_types=1);

namespace GAR\Repository;

use GAR\Exceptions\CodeNotFoundException;
use GAR\Exceptions\CodeTypeNotFoundException;

/**
 * Enums type of codes
 */
enum Codes : string
{
    case OKATO = 'okato';
    case OKTMO = 'oktmo';
    case KLADR = 'kladr';
    case ALL = 'all';

	/**
	 * @param string $type
	 * @return string
	 * @throws CodeTypeNotFoundException - if code type not found
	 */
	static function tryFindWithException(string $type): string
	{
		$try = self::tryFrom($type);
		if (null === $try) {
			throw new CodeTypeNotFoundException($type, self::cases());
		}
		return $type;
	}
}
