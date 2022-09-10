<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Join;

interface JoinAble
{
	/**
	 * Create INNER JOIN template
	 *
	 * @param array<string, string>|string $table - name of table
	 * @param array<string|int, string> $condition - ON condition by fliedName = filedName
	 * @return JoinQuery
	 */
	public function innerJoin(array|string $table, array $condition): JoinQuery;

	/**
	 * Create LEFT OUTER JOIN template
	 *
	 * @param array<string, string>|string $table - name of table
	 * @param array<string|int, string> $condition - ON condition by fliedName = filedName
	 * @return JoinQuery
	 */
	public function leftJoin(array|string $table, array $condition): JoinQuery;

	/**
	 * Create RIGHT OUTER JOIN template
	 *
	 * @param array<string, string>|string $table - name of table
	 * @param array<string|int, string> $condition - ON condition by fliedName = filedName
	 * @return JoinQuery
	 */
	public function rightJoin(array|string $table, array $condition): JoinQuery;
}