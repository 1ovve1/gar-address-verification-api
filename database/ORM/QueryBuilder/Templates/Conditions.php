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
	 * @param string $operation
	 * @return string
	 */
	public static function tryFind(string $operation): string
	{
		$condition = self::tryFrom(trim($operation));

		return $condition?->value ?? throw new OperationNotFoundException($operation);
	}
}