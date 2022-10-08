<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Where;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

interface WhereAble
{
	/**
	 * Create WHERE template
	 *
	 * @param callable():ActiveRecord|array<string|int, string>|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract $value - value to compare
	 * @return WhereQuery
	 */
	public function where(callable|array|string $field_or_nested_clbk,
	                      float|int|bool|string|null $sign_or_value = '',
	                      float|int|bool|string|null $value = null): WhereQuery;
}