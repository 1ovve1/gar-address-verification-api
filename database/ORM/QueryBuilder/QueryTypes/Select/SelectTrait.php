<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\ORM\DBFacade;

trait SelectTrait
{
	/**
	 * {@inheritDoc}
	 */
	public static function select(array|string $fields,
	                              null|array|string $anotherTables = null): SelectQuery
	{

		$fields = match(gettype($fields)) {
			"string" => $fields,
			"array" => implode(', ', $fields)
		};
		$anotherTables = match(gettype($anotherTables)) {
			"NULL" => DBFacade::genTableNameByClassName(static::class),
			"string" => $anotherTables,
			"array" => implode(', ', $anotherTables)
		};

		return new SelectQueryImpl($fields, $anotherTables);
	}
}