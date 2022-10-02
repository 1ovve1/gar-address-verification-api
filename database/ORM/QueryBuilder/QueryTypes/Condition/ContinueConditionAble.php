<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;

interface ContinueConditionAble
{
	/**
	 * Create AND WHERE template
	 *
	 * @param callable|array<string, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param int|float|bool|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param float|int|bool|string|null $value - value to compare
	 * @return ContinueConditionQuery
	 */
	public function andWhere(callable|array|string $field_or_nested_clbk,
	                         int|float|bool|string|null $sign_or_value = null,
	                         float|int|bool|string|null $value = null): ContinueConditionQuery;

	/**
	 * Create OR WHERE template
	 *
	 * @param callable|array<string, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param int|float|bool|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param float|int|bool|string|null $value - value to compare
	 * @return ContinueConditionQuery
	 */
	public function orWhere(callable|array|string $field_or_nested_clbk,
	                        int|float|bool|string|null $sign_or_value = null,
	                        float|int|bool|string|null $value = null): ContinueConditionQuery;
}