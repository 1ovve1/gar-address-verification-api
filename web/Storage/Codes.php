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
	 * @param  mixed $type
	 * @return Codes
	 * @throws CodeTypeNotFoundException - if code type not found
	 */
	static function tryFindWithException(mixed $type): self
	{
		$try = null;

		if (is_string($type)) {
			$try = self::tryFrom(strtoupper($type));
		}

		if (null === $try) {
			throw new CodeTypeNotFoundException($type, self::cases());
		}

		return $try;
	}
}
