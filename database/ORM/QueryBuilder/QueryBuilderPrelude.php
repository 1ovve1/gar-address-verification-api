<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\QueryBuilder\QueryTypes\Select\SelectAble;
use DB\ORM\QueryBuilder\QueryTypes\Select\SelectTrait;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;
use DB\ORM\QueryBuilder\AbstractSQL\{
	DeleteQuery, EndQuery, SelectQuery, UpdateQuery
};

/**
 * Common interface for query builder
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
abstract class QueryBuilderPrelude
	extends ActiveRecordImpl
	implements SelectAble
{
use SelectTrait;
//	/**
//	 * Create insert template
//	 *
//	 * @param  array<string, DatabaseContract> $values - values in field => value fmt
//	 * @param  string|null $tableName - name of table
//	 * @return EndQuery
//	 */
//	public static function insert(array $values,
//	                              ?string $tableName = null): EndQuery;

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