<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Join;

use DB\ORM\QueryBuilder\QueryTypes\Select\SelectQuery;

interface JoinAble
{
	/**
	 * Create INNER JOIN template
	 *
	 * @param string $table - name of table
	 * @param array<string|int, string> $condition - ON condition by fliedName = filedName
	 * @return JoinQuery
	 */
	public function innerJoin(string $table, array $condition): JoinQuery;

	/**
	 * Create LEFT OUTER JOIN template
	 *
	 * @param string $table - name of table
	 * @param array<string|int, string> $condition - ON condition by fliedName = filedName
	 * @return JoinQuery
	 */
	public function leftJoin(string $table, array $condition): JoinQuery;

	/**
	 * Create RIGHT OUTER JOIN template
	 *
	 * @param string $table - name of table
	 * @param array<string|int, string> $condition - ON condition by fliedName = filedName
	 * @return JoinQuery
	 */
	public function rightJoin(string $table, array $condition): JoinQuery;
}