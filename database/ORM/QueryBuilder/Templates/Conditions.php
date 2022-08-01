<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\Templates;

enum Conditions: string
{
	case EQ = '=';
	case LOW = '<';
	case LOW_EQ = '<=';
	case HIGH = '>';
	case HIGH_EQ = '>=';
	case LIKE = 'LIKE';
	case IN = 'IN';

	public static function tryFind(mixed $operation): string|false
	{
		if (!is_string($operation)) {
			return false;
		}
		return self::tryFrom(trim($operation))?->value ?? false;
	}
}