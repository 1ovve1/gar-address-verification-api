<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Where;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereQuery;

interface WhereAble
{
	/**
	 * Create WHERE template
	 *
	 * @param callable|array|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param int|float|bool|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param float|int|bool|string|null $value - value to compare
	 * @return WhereQuery
	 */
	public function where(callable|array|string $field_or_nested_clbk,
	                      int|float|bool|string|null $sign_or_value = '',
	                      float|int|bool|string|null $value = null): WhereQuery;
}