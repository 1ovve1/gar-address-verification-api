<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\Templates;

use DB\Exceptions\Unchecked\OperationNotFoundException;

enum Conditions: string
{
	case EQ = '=';
	case LOW = '<';
	case LOW_EQ = '<=';
	case HIGH = '>';
	case HIGH_EQ = '>=';
	case LIKE = 'LIKE';
	case IN = 'IN';

	/**
	 * @param mixed $operation
	 * @return string
	 */
	public static function tryFind(mixed $operation): string
	{
		if (is_string($operation)) {
			$condition = self::tryFrom(trim($operation));
		}

		return $condition?->value ?? throw new OperationNotFoundException($operation);
	}
}