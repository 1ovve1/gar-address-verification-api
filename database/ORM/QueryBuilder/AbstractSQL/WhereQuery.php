<?php

declare(strict_types=1);

namespace DB\ORM\QueryBuilder\AbstractSQL;

/**
 * Continue where interface
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface WhereQuery
{
	/**
	 * Create AND WHERE template
	 *
	 * @param  string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param  DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param  DatabaseContract|null $value - value to compare
	 * @return WhereQuery
	 */
	public function andWhere(string|callable $field_or_nested_clbk,
	                         mixed $sign_or_value = null,
	                         mixed $value = null): WhereQuery;

    /**
     * Create OR WHERE template
     *
     * @param  string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
     * @param  DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
     * @param  DatabaseContract|null $value - value to compare
     * @return WhereQuery
     */
    public function orWhere(string|callable $field_or_nested_clbk,
                            mixed $sign_or_value = null,
                            mixed $value = null): WhereQuery;

    /**
     * Creating ORDER BY template
     *
     * @param  string $field - field to sort
     * @param  bool $asc - type of sort
     * @return EndQuery
     */
    public function orderBy(string $field, bool $asc = true): EndQuery;

    /**
     * Create LIMIT $count template
     * @param  positive-int $count - limit count
     * @return EndQuery
     */
    public function limit(int $count): EndQuery;
}
