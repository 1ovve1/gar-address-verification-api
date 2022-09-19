<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\Templates\PgSQL;

use DB\Exceptions\Unchecked\OperationNotFoundException;
use DB\ORM\QueryBuilder\Templates\Conditions;

enum ConditionsPgSQL: string
	implements Conditions
{
	case EQ = '=';
	case LOW = '<';
	case LOW_EQ = '<=';
	case HIGH = '>';
	case HIGH_EQ = '>=';
	case LIKE = 'LIKE';
	case ILIKE = 'ILIKE';
	case IN = 'IN';


	static function tryFind(string $dryInput): string
	{
		$condition = self::tryFrom(strtoupper(trim($dryInput)));

		// you wanna do manual 'LIKE' compare using pgsl? that he's implement
		if ($condition == self::LIKE) {
			// SICK! THAT'S A WRONG IMPLEMENT
			$condition = self::ILIKE;
		}

		return $condition?->value ?? throw new OperationNotFoundException($dryInput);
	}

}