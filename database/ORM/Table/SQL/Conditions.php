<?php declare(strict_types=1);

namespace DB\ORM\Table\SQL;

enum Conditions: string
{
	case EQ = '=';
	case LOW = '<';
	case LOW_EQ = '<=';
	case HIGH = '>';
	case HIGH_EQ = '>=';
	case LIKE = 'LIKE';
	case IN = 'IN';

	public static function tryFind(string $operation): bool
	{
		return self::tryFrom(trim($operation)) !== null;
	}
}