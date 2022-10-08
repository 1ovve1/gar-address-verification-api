<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

/**
 * Continue where interface
 */
interface ContinueWhereAble
{
	/**
	 * Create AND WHERE template
	 *
	 * @param callable():ActiveRecord|array<string, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract $value - value to compare
	 * @return ContinueWhereQuery
	 */
	public function andWhere(callable|array|string $field_or_nested_clbk,
	                         int|float|bool|string|null $sign_or_value = null,
	                         int|float|bool|string|null $value = null): ContinueWhereQuery;

	/**
	 * Create OR WHERE template
	 *
	 * @param callable():ActiveRecord|array<string, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract $value - value to compare
	 * @return ContinueWhereQuery
	 */
	public function orWhere(callable|array|string $field_or_nested_clbk,
	                        int|float|bool|string|null $sign_or_value = null,
	                        int|float|bool|string|null $value = null): ContinueWhereQuery;
}