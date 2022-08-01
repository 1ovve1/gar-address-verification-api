<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\Select\SelectQueryImpl;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

class QueryBuilder extends QueryBuilderPrelude
{
//	/**
//	 * {@inheritDoc}
//	 */
//	public static function insert(array $values,
//	                              ?string $tableName = null): InsertQuery
//	{
//		return new InsertQueryImpl($values, $tableName);
//	}



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