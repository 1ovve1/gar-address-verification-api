<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;



interface NestedConditionAble
{
	/**
	 * Create WHERE template
	 *
	 * @param callable|array|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param int|float|bool|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param float|int|bool|string|null $value - value to compare
	 * @return NestedConditionQuery
	 */
	public static function where(callable|array|string $field_or_nested_clbk,
	                             float|bool|int|string|null $sign_or_value = '',
	                             float|bool|int|string|null $value = null): NestedConditionQuery;
}