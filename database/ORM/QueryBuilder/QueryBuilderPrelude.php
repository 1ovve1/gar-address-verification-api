<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\Insert\InsertAble;
use DB\ORM\QueryBuilder\QueryTypes\Insert\InsertTrait;
use DB\ORM\QueryBuilder\QueryTypes\Select\SelectAble;
use DB\ORM\QueryBuilder\QueryTypes\Select\SelectTrait;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
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
	implements SelectAble, InsertAble, BuilderOptions
{
use SelectTrait, InsertTrait;
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

	/**
	 * {@inheritDoc}
	 */
	public static function findFirst(string $field,
	                                 mixed $value,
	                                 ?string $anotherTable = null): array
	{
		return static::select($field, $anotherTable)->where($field, $value)->save();
	}

	/**
	 * @inheritDoc
	 */
	public static function createStateIfNotExist(mixed $tryState, callable $stateInstruction): ActiveRecord
	{
		if (!($tryState instanceof ActiveRecord)) {
			$tryCallback = $stateInstruction();
			if (!($tryCallback instanceof ActiveRecord)) {
				DBFacade::dumpException(
					null,
					'Callback should return ActiveRecord state, but return ' . gettype($tryCallback),
					func_get_args()
				);
			}
			$tryState = $tryCallback;
		}

		return $tryState;
	}


}