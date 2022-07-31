<?php declare(strict_types=1);

namespace DB\ORM\Table\QueryTypes\Where;

use DB\ORM\Table\QueryTypes\EndQuery\LimitImpl;
use DB\ORM\Table\QueryTypes\EndQuery\OrderByImpl;
use DB\ORM\Table\SQL\WhereQuery;
use DB\ORM\Table\Templates\SQL;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\ActiveRecordImpl;
use DB\ORM\Table\Utils\WhereArgsHandler;

class WhereImpl implements WhereQuery, ActiveRecord
{
use ActiveRecordImpl, WhereArgsHandler;

	public function __construct(ActiveRecord $parent,
	                            string $field,
	                            string $sign,
	                            float|int|bool|string $value)
	{
		$this->initQueryBox(
			template: SQL::WHERE,
			clearArgs: [$field, $sign],
			dryArgs: [$value],
			parentBox: $parent->getQueryBox()
		);
	}

	/**
	 * @inheritDoc
	 */
	public function andWhere(callable|string $field_or_nested_clbk, mixed $sign_or_value = null, mixed $value = null): WhereImpl
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
			return new NestedWhereImpl($this, $callback);
		}
		$field = $field_or_nested_clbk;

		[$field, $sign, $value] = $this->handleWhereArgs($field, $sign_or_value, $value);

		return new WhereAndImpl($this, $field, $sign, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function orWhere(callable|string $field_or_nested_clbk, mixed $sign_or_value = null, mixed $value = null): WhereImpl
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
			return new NestedWhereImpl($this, $callback);
		}
		$field = $field_or_nested_clbk;

		[$field, $sign, $value] = $this->handleWhereArgs($field, $sign_or_value, $value);

		return new WhereOrImpl($this, $field, $sign, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function orderBy(string $field, bool $asc = true): OrderByImpl
	{
		return new OrderByImpl($this, $field, $asc);
	}

	/**
	 * @inheritDoc
	 */
	public function limit(int $count): LimitImpl
	{
		return new LimitImpl($this, $count);
	}
}