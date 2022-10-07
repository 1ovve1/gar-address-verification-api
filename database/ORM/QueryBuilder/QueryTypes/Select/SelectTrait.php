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
	                              callable|array|string|null $anotherTables = null): SelectQuery
	{
		$fields = match(is_string($fields)) {
			true =>	$fields,
			false => DBFacade::fieldsWithPseudonymsToString($fields),
		};

		$anotherTables = match(is_null($anotherTables)) {
			true => QueryBuilder::tableQuoted(static::class) ,
			default => match (is_array($anotherTables)) {
				true => DBFacade::tableNamesWithPseudonymsToString($anotherTables),
				default => $anotherTables
			}
		};

		return match(is_callable($callback = $anotherTables)) {
			true => new ImplSubSelect($fields, $callback),
			default => new ImplSelect($fields, $anotherTables)
		};
	}
}