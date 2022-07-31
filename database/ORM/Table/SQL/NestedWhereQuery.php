<?php declare(strict_types=1);

namespace DB\ORM\Table\SQL;

/**
 * Defines methods for nested sql-where queries
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface NestedWhereQuery
{
	/**
	 * Create WHERE template
	 *
	 * @param  string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param  DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param  DatabaseContract|null $value - value to compare
	 * @return NestedWhereQuery
	 */
	public function where(string|callable $field_or_nested_clbk,
	                      mixed $sign_or_value = null,
	                      mixed $value = null): NestedWhereQuery;

	/**
	 * Create AND WHERE template
	 *
	 * @param  string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param  DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param  DatabaseContract|null $value - value to compare
	 * @return NestedWhereQuery
	 */
	public function andWhere(string|callable $field_or_nested_clbk,
	                         mixed $sign_or_value = null,
	                         mixed $value = null): NestedWhereQuery;

	/**
	 * Create OR WHERE template
	 *
	 * @param  string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param  DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param  DatabaseContract|null $value - value to compare
	 * @return NestedWhereQuery
	 */
	public function orWhere(string|callable $field_or_nested_clbk,
	                        mixed $sign_or_value = null,
	                        mixed $value = null): NestedWhereQuery;
}