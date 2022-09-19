<?php

declare(strict_types=1);

namespace GAR\Storage;

use GAR\Exceptions\Unchecked\CodeTypeNotFoundException;

/**
 * Enums type of codes
 */
enum Codes : string
{
    case OKATO = 'OKATO';
    case OKTMO = 'OKTMO';
    case KLADR = 'KLADR';
    case ALL = 'ALL';

	/**
	 * @param string $type
	 * @return  Codes
	 * @throws CodeTypeNotFoundException - if code type not found
	 */
	static function tryFindWithException(string $type): self
	{
		$try = self::tryFrom(strtoupper($type));
		if (null === $try) {
			throw new CodeTypeNotFoundException($type, self::cases());
		}
		return $try;
	}
}
