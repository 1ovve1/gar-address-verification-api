<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

interface QueryResult
{
	public const PDO_F_ASSOC = \PDO::FETCH_ASSOC;
	public const PDO_F_COL = \PDO::FETCH_COLUMN;
	public const PDO_F_BOTH = \PDO::FETCH_BOTH;

	/**
	 * Fetching last query by special flag
	 *
	 * @param int $flag - fetching flag
	 * @return array<mixed>|false
	 */
	function fetchAll(int $flag = \PDO::FETCH_ASSOC): array|false;

	/**
	 * Return rows count in query result
	 * @return int
	 */
	function rowCount(): int;

	/**
	 * Check if result is empty
	 * @return bool
	 */
	function isEmpty(): bool;

	/**
	 * Check if result has many rows
	 * @return bool
	 */
	function hasManyRows(): bool;
}