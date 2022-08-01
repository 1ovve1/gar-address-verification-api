<?php

declare(strict_types=1);

namespace DB\ORM\QueryBuilder\AbstractSQL;

/**
 * Select query interface
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface SelectQuery
{
	/**
	 * Create WHERE template
	 *
	 * @param string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param string $sign_or_value - sign for compare or value for default '=' compare
	 * @param float|int|bool|string|null $value - value to compare
	 * @return WhereQuery
	 */
    public function where(string|callable $field_or_nested_clbk,
                          string $sign_or_value = '',
                          float|int|bool|string|null $value = null): WhereQuery;

    /**
     * Create INNER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function innerJoin(string $table, array $condition): SelectQuery;

    /**
     * Create LEFT OUTER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function leftJoin(string $table, array $condition): SelectQuery;

    /**
     * Create RIGHT OUTER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function rightJoin(string $table, array $condition): SelectQuery;

    /**
     * Create LIMIT $count template
     * @param  positive-int $count - limit count
     * @return EndQuery
     */
    public function limit(int $count): EndQuery;

    /**
     * Creating ORDER BY template
     *
     * @param  string $field - name of field
     * @param  bool|boolean $asc - type of sort
     * @return EndQuery
     */
    public function orderBy(string $field, bool $asc = true): EndQuery;

}
