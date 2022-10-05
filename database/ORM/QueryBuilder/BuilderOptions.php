<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBAdapter\QueryResult;


/**
 * Common interface for query builder
 */
interface BuilderOptions
{
	/**
	 * Finding first element of $field column with $value compare
	 *
	 * @param  string $field - fields name
	 * @param  DatabaseContract $value - value for compare
	 * @param  string|null $anotherTable - table name
	 * @return QueryResult
	 */
	static function findFirst(string $field,
                              mixed $value,
                              ?string $anotherTable = null): QueryResult;

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
	 * @param string|null $className
	 * @return string - table name of current pseudo-model
	 */
	static function table(?string $className = null): string;

	/**
	 * @param string|null $className
	 * @return string - table name of current pseudo-model in quoted wrap
	 */
	static function tableQuoted(?string $className = null): string;
}