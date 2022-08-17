<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\AbstractSQL\EndQuery;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;


/**
 * Common interface for query builder
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface BuilderOptions
{
	/**
	 * Finding first element of $field collumn with $value compare
	 *
	 * @param  string $field - fields name
	 * @param  DatabaseContract $value - value for compare
	 * @param  string|null $anotherTable - table name
	 * @return array<mixed>
	 */
	static function findFirst(string $field,
                              mixed $value,
                              ?string $anotherTable = null): array;

	/**
	 * Doing force insert into table with huge SQL query
	 *
	 * @param array<DatabaseContract> $values - values for the force insert
	 * @return QueryResult
	 */
	function forceInsert(array $values): QueryResult;

	/**
	 * Save changes in forceInsert buffer
	 *
	 * @return QueryResult
	 */
	function saveForceInsert(): QueryResult;

	/**
	 * @return string - table name of current pseudo-model
	 */
	static function getTableName(): string;
}