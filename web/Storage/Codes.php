<?php

declare(strict_types=1);

namespace GAR\Storage;

use GAR\Exceptions\Unchecked\CodeTypeNotFoundException;

/**
 * Enums type of codes
 */
enum Codes : string
{
	case IFNSFL = 'IFNSFL';
	case IFNSUL = 'IFNSUL';
	case TER_IFNSFL = 'territorialifnsflcode';
	case TER_IFNSUL = 'territorialifnsulcode';
	case POST = 'Postindex';
	case OKATO = 'OKATO';
	case OKTMO = 'OKTMO';
	case CADASTR = 'CadastrNum';
	case KLADR = 'CODE';
	case PLAINKLADR = 'PLAINKLADR';
	case REGUIONCODE = 'REGIONCODE';
	case REESTRNUM = 'ReestrNum';
	case DIVISIONTYPE = 'DivisionType';
	case COUNTER = 'Counter';
	case OFFICIAL = 'Official';
	case POSTSTATUS = 'PostStatus';
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
