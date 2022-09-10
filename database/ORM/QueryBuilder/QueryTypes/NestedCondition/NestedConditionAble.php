<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;



interface NestedConditionAble
{
	/**
	 * Create WHERE template
	 *
	 * @param callable|array<string|string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract $value - value to compare
	 * @return NestedConditionQuery
	 */
	public static function where(callable|array|string $field_or_nested_clbk,
	                             int|float|string|bool|null $sign_or_value = '',
	                             int|float|string|bool|null $value = null): NestedConditionQuery;
}