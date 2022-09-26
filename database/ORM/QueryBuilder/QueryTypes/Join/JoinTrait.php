<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Join;

use DB\ORM\DBFacade;

trait JoinTrait
{
	/**
	 * @inheritDoc
	 */
	public function innerJoin(array|string $table, array $condition): JoinQuery
	{
		['tableName' => $table, 'condition' => [$leftSideField, $rightSideField]] = DBFacade::joinArgsHandler($table, $condition);

		return new ImplInnerJoin($this, $table, $leftSideField, $rightSideField);
	}

	/**
	 * @inheritDoc
	 */
	public function leftJoin(array|string $table, array $condition): JoinQuery
	{
		['tableName' => $table, 'condition' => [$leftSideField, $rightSideField]] = DBFacade::joinArgsHandler($table, $condition);

		return new ImplLeftJoin($this, $table, $leftSideField, $rightSideField);
	}

	/**
	 * @inheritDoc
	 */
	public function rightJoin(array|string $table, array $condition): JoinQuery
	{
		['tableName' => $table, 'condition' => [$leftSideField, $rightSideField]] = DBFacade::joinArgsHandler($table, $condition);


		return new ImplRightJoin($this, $table, $leftSideField, $rightSideField);
	}


}