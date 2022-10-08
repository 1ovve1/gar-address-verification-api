<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

interface ConditionAble
{
	/**
	 * Create WHERE template
	 *
	 * @param callable():ActiveRecord|array<int|string, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract $value - value to compare
	 * @return ConditionQuery
	 */
	public static function where(callable|array|string $field_or_nested_clbk,
	                             int|float|string|bool|null $sign_or_value = '',
	                             int|float|string|bool|null $value = null): ConditionQuery;
}