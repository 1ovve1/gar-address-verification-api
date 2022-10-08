<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

interface ContinueConditionAble
{
	/**
	 * Create AND condition template
	 *
	 * @param callable():ActiveRecord|array<int|string, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract $value - value to compare
	 * @return ContinueConditionQuery
	 */
	public function andWhere(callable|array|string $field_or_nested_clbk,
	                         int|float|bool|string|null $sign_or_value = null,
	                         int|float|bool|string|null $value = null): ContinueConditionQuery;

	/**
	 * Create OR condition template
	 *
	 * @param callable():ActiveRecord|array<int|string, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract $value - value to compare
	 * @return ContinueConditionQuery
	 */
	public function orWhere(callable|array|string $field_or_nested_clbk,
	                        int|float|bool|string|null $sign_or_value = null,
	                        int|float|bool|string|null $value = null): ContinueConditionQuery;
}