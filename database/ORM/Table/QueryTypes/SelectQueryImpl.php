<?php declare(strict_types=1);

namespace DB\ORM\Table\QueryTypes;

use DB\ORM\DBFacade;
use DB\ORM\Table\QueryTypes\EndQuery\LimitImpl;
use DB\ORM\Table\QueryTypes\EndQuery\OrderByImpl;
use DB\ORM\Table\QueryTypes\Join\InnerJoinImpl;
use DB\ORM\Table\QueryTypes\Join\LeftJoinImpl;
use DB\ORM\Table\QueryTypes\Join\RightJoinImpl;
use DB\ORM\Table\QueryTypes\Where\NestedWhereImpl;
use DB\ORM\Table\QueryTypes\Where\WhereImpl;
use DB\ORM\Table\SQL\EndQuery;
use DB\ORM\Table\SQL\SelectQuery;
use DB\ORM\Table\SQL\WhereQuery;
use DB\ORM\Table\Templates\SQL;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\ActiveRecordImpl;
use DB\ORM\Table\Utils\JoinArgsHandler;
use DB\ORM\Table\Utils\QueryBox;
use DB\ORM\Table\Utils\WhereArgsHandler;

class SelectQueryImpl implements SelectQuery, ActiveRecord
{
use ActiveRecordImpl, WhereArgsHandler, JoinArgsHandler;

	/**
	 * @param string $fields
	 * @param string $anotherTables
	 */
	function __construct(string $fields,
	                     string $anotherTables)
	{
		$this->initQueryBox(
			template: SQL::SELECT, clearArgs: [$fields, $anotherTables]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function where(callable|string $field_or_nested_clbk,
	                      string $sign_or_value = '',
	                      float|int|bool|string|null $value = null): WhereImpl
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
			return new NestedWhereImpl($this, $callback);
		}
		$field = $field_or_nested_clbk;

		[$field, $sign, $value] = $this->handleWhereArgs($field, $sign_or_value, $value);

		return new WhereImpl($this, $field, $sign, $value);

	}

	/**
	 * @inheritDoc
	 */
	public function innerJoin(string $table, array $condition): InnerJoinImpl
	{
		[$leftSideField, $rightSideField] = $this->joinArgsHandler($table, $condition);

		return new InnerJoinImpl($this, $table, $leftSideField, $rightSideField);
	}

	/**
	 * @inheritDoc
	 */
	public function leftJoin(string $table, array $condition): LeftJoinImpl
	{
		[$leftSideField, $rightSideField] = $this->joinArgsHandler($table, $condition);

		return new LeftJoinImpl($this, $table, $leftSideField, $rightSideField);
	}

	/**
	 * @inheritDoc
	 */
	public function rightJoin(string $table, array $condition): RightJoinImpl
	{
		[$leftSideField, $rightSideField] = $this->joinArgsHandler($table, $condition);

		return new RightJoinImpl($this, $table, $leftSideField, $rightSideField);
	}

	/**
	 * @inheritDoc
	 */
	public function limit(int $count): LimitImpl
	{
		if ($count <= 0) {
			DBFacade::dumpException($this, '$count should be 1 or higer', func_get_args());
		}

		return new LimitImpl($this, $count);
	}

	/**
	 * @inheritDoc
	 */
	public function orderBy(string $field, bool $asc = true): OrderByImpl
	{
		return new OrderByImpl($this, $field, $asc);
	}


}