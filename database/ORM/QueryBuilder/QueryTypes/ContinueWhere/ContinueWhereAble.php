<?php

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;

use DB\ORM\QueryBuilder\AbstractSQL\WhereQuery;

/**
 * Continue where interface
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface ContinueWhereAble
{
	/**
	 * Create AND WHERE template
	 *
	 * @param  callable|string $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param  DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param  DatabaseContract|null $value - value to compare
	 * @return ContinueWhereQuery
	 */
	public function andWhere(callable|string $field_or_nested_clbk,
	                         mixed $sign_or_value = null,
	                         mixed $value = null): ContinueWhereQuery;

	/**
	 * Create OR WHERE template
	 *
	 * @param string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param DatabaseContract|null $value - value to compare
	 * @return ContinueWhereQuery
	 */
	public function orWhere(callable|string $field_or_nested_clbk,
	                        mixed $sign_or_value = null,
	                        mixed $value = null): ContinueWhereQuery;
}