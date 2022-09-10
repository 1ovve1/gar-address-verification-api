<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryBuilder;
use InvalidArgumentException;

trait SelectTrait
{
	/**
	 * {@inheritDoc}
	 */
	public static function select(array|string $fields,
	                              null|array|string $anotherTables = null): SelectQuery
	{

		$fields = match($type = gettype($fields)) {
			"string" => $fields,
			"array" => DBFacade::fieldsWithPseudonymsToString($fields),
			default => throw new InvalidArgumentException("Type '{$type}' are unknown")
		};
		$anotherTables = match($type = gettype($anotherTables)) {
			"NULL" => QueryBuilder::table(static::class),
			"string" => $anotherTables,
			"array" => DBFacade::tableNamesWithPseudonymsToString($anotherTables),
			default => throw new InvalidArgumentException("Type '{$type}' are unknown")
		};

		return new ImplSelect($fields, $anotherTables);
	}
}