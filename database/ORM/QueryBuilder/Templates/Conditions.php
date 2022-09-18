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
	case LIKE_PGSQL = 'ILIKE';
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

		if (isset($condition) && $_ENV['DB_TYPE'] === 'pgsql') {
			if ($condition == self::LIKE) {
				$condition = self::LIKE_PGSQL;
			}
		}

		return $condition?->value ?? throw new OperationNotFoundException($operation);
	}
}