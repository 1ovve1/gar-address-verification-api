<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;

use DB\ORM\DBFacade;

trait ConditionTrait
{
	/**
	 * @inheritDoc
	 */
	public static function where(callable|array|string $field_or_nested_clbk,
	                             mixed $sign_or_value = '',
	                             mixed $value = null): ConditionQuery
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
			return new ImplNestedCondition($callback);
		}

		['field' => $field, 'sign' => $sign, 'value' => $value] = DBFacade::whereArgsHandler($field_or_nested_clbk, $sign_or_value, $value);

		return new ImplCondition($field, $sign, $value);
	}
}