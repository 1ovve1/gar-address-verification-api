<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\DBFacade;

trait NestedContinueConditionTrait
{
	/**
	 * @inheritDoc
	 */
	public function andWhere(callable|array|string $field_or_nested_clbk,
	                         int|float|bool|string|null $sign_or_value = null,
	                         float|int|bool|string|null $value = null): NestedContinueConditionQuery
	{
		if (is_callable($field_or_nested_clbk)) {
			return new ImplNestedInNestedAnd($this, $field_or_nested_clbk);
		}

		[$field, $sign, $value] = DBFacade::whereArgsHandler($field_or_nested_clbk, $sign_or_value, $value);


		return new ImplNestedConditionAnd($this, $field, $sign, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function orWhere(callable|array|string $field_or_nested_clbk,
	                        int|float|bool|string|null $sign_or_value = null,
	                        float|int|bool|string|null $value = null): NestedContinueConditionQuery
	{
		if (is_callable($field_or_nested_clbk)) {
			return new ImplNestedInNestedOr($this, $field_or_nested_clbk);
		}

		[$field, $sign, $value] = DBFacade::whereArgsHandler($field_or_nested_clbk, $sign_or_value, $value);

		return new ImplNestedConditionOr($this, $field, $sign, $value);
	}
}