<?php

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;


use DB\ORM\QueryBuilder\Utils\WhereArgsHandler;

trait ContinueWhereTrait
{
use WhereArgsHandler;

	/**
	 * @inheritDoc
	 */
	public function andWhere(callable|string $field_or_nested_clbk,
	                         mixed $sign_or_value = null,
	                         mixed $value = null): ContinueWhereQuery
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
//			return new ImplNestedWhere($this, $callback);
		}
		$field = $field_or_nested_clbk;

		[$field, $sign, $value] = $this::handleWhereArgs($field, $sign_or_value, $value);

		return new ImplWhereAnd($this, $field, $sign, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function orWhere(callable|string $field_or_nested_clbk,
	                        mixed $sign_or_value = null,
	                        mixed $value = null): ContinueWhereQuery
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
//			return new ImplNestedWhere($this, $callback);
		}
		$field = $field_or_nested_clbk;

		[$field, $sign, $value] = $this::handleWhereArgs($field, $sign_or_value, $value);

		return new ImplWhereOr($this, $field, $sign, $value);
	}
}