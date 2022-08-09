<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\DBFacade;

trait NestedConditionTrait
{
	/**
	 * @inheritDoc
	 */
	public static function where(callable|array|string $field_or_nested_clbk,
	                             float|bool|int|string|null $sign_or_value = '',
	                             float|bool|int|string|null $value = null): NestedConditionQuery
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
			return new ImplNestedInNested($callback);
		}

		[$field, $sign, $value] = DBFacade::whereArgsHandler($field_or_nested_clbk, $sign_or_value, $value);

		return new ImplNestedCondition($field, $sign, $value);
	}
}