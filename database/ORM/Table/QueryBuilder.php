<?php declare(strict_types=1);

namespace DB\ORM\Table;

use DB\ORM\DBFacade;
use DB\ORM\Table\QueryTypes\SelectQueryImpl;
use DB\ORM\Table\SQL\DeleteQuery;
use DB\ORM\Table\SQL\EndQuery;
use DB\ORM\Table\SQL\SelectQuery;
use DB\ORM\Table\SQL\UpdateQuery;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\ActiveRecordImpl;

class QueryBuilder implements QueryBuilderPrelude, BuilderOptions, ActiveRecord
{
use ActiveRecordImpl;
//	/**
//	 * {@inheritDoc}
//	 */
//	public static function insert(array $values,
//	                              ?string $tableName = null): InsertQuery
//	{
//		return new InsertQueryImpl($values, $tableName);
//	}

	/**
	 * {@inheritDoc}
	 */
	public static function select(array|string $fields,
	                              null|array|string $anotherTables = null): SelectQueryImpl
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

//	/**
//	 * {@inheritDoc}
//	 */
//	public static function update(string $field,
//	                              mixed $value,
//	                              ?string $tableName = null): UpdateQuery
//	{
//	}
//
//	/**
//	 * {@inheritDoc}
//	 */
//	public static function delete(?string $tableName = null): DeleteQuery
//	{
//	}

	/**
	 * {@inheritDoc}
	 */
	public static function findFirst(string $field,
	                                 mixed $value,
	                                 ?string $anotherTable = null): array
	{
		// TODO: Implement findFirst() method.
		return [];
	}

	/**
	 * {@inheritDoc}
	 */
	public static function createStateIfNotExist(mixed $tryState,
	                                             callable $stateInstruction): bool
	{
		// TODO: Implement createStateIfNotExist() method.
		return false;
	}

	public static function getTableNameFromClassname(): string
	{
		return DBFacade::genTableNameByClassName(static::class);
	}


}