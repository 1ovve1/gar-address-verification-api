<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

use PDO;

interface QueryResult
{
	public const PDO_F_ASSOC = PDO::FETCH_ASSOC;
	public const PDO_F_COL = PDO::FETCH_COLUMN;
	public const PDO_F_BOTH = PDO::FETCH_BOTH;
	public const PDO_F_NUM = PDO::FETCH_NUM;

	/**
	 * Fetching last query by special flag
	 *
	 * @param int $flag - fetching flag
	 * @return array<int, array<int|string, mixed>>
	 */
	function fetchAll(int $flag = PDO::FETCH_ASSOC): array;

	/**
	 * Alias for fetchAll with assoc keys array
	 * @return array<int, array<string, mixed>>
	 */
	function fetchAllAssoc(): array;

	/**
	 * Alias for fetchAll with num keys array
	 * @return array<int, array<int, mixed>>
	 */
	function fetchAllNum(): array;

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
	 * Alias for !isEmpty()
	 * @return bool
	 */
	function isNotEmpty(): bool;

	/**
	 * Check if result have multiple strings
	 * @return bool
	 */
	function hasOnlyOneRow(): bool;

	/**
	 * Alias for !hasOneRow()
	 * @return bool
	 */
	function hasManyRows(): bool;
}