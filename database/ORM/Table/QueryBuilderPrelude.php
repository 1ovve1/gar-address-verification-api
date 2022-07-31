<?php declare(strict_types=1);

namespace DB\ORM\Table;

use DB\ORM\Table\SQL\{
	DeleteQuery, EndQuery, SelectQuery, UpdateQuery
};

/**
 * Common interface for query builder
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface QueryBuilderPrelude
{
//	/**
//	 * Create insert template
//	 *
//	 * @param  array<string, DatabaseContract> $values - values in field => value fmt
//	 * @param  string|null $tableName - name of table
//	 * @return EndQuery
//	 */
//	public static function insert(array $values,
//	                              ?string $tableName = null): EndQuery;


	/**
	 * Creating select template
	 *
	 * @param  array<string>|string $fields - fields to select
	 * @param  array<string>|string|null $anotherTables - name of another table
	 * @return SelectQuery
	 */
	public static function select(array|string $fields,
	                              null|array|string $anotherTables = null): SelectQuery;

//	/**
//	 * Create update template
//	 *
//	 * @param  string $field - field for update
//	 * @param  DatabaseContract $value - value for upadte
//	 * @param  string|null $tableName - name of table
//	 * @return UpdateQuery
//	 */
//	public static function update(string $field,
//	                              mixed $value,

//	                              ?string $tableName = null): UpdateQuery;
//	/**
//	 * Creating delete template
//	 *
//	 * @param  string|null $tableName - name of table
//	 * @return DeleteQuery
//	 */

//	public static function delete(?string $tableName = null): DeleteQuery;

}