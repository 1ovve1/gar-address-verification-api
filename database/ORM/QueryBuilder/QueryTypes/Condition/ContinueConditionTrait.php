<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;

use DB\ORM\DBFacade;

trait ContinueConditionTrait
{
	/**
	 * @inheritDoc
	 */
	public function andWhere(callable|array|string $field_or_nested_clbk,
	                         int|float|bool|string|null $sign_or_value = null,
	                         int|float|bool|string|null $value = null): ContinueConditionQuery
	{
		if (is_callable($field_or_nested_clbk)) {
			return new ImplNestedConditionAnd($this, $field_or_nested_clbk);
		}

		['field' => $field, 'sign' => $sign, 'value' => $value] = DBFacade::whereArgsHandler($field_or_nested_clbk, $sign_or_value, $value);


		return new ImplConditionAnd($this, $field, $sign, $value);
	}

	/**
	 * @inheritDoc
	 */
	public function orWhere(callable|array|string $field_or_nested_clbk,
	                        int|float|bool|string|null $sign_or_value = null,
	                        int|float|bool|string|null $value = null): ContinueConditionQuery
	{
		if (is_callable($field_or_nested_clbk)) {
			return new ImplNestedConditionOr($this, $field_or_nested_clbk);
		}

		['field' => $field, 'sign' => $sign, 'value' => $value] = DBFacade::whereArgsHandler($field_or_nested_clbk, $sign_or_value, $value);

		return new ImplConditionOr($this, $field, $sign, $value);
	}
}