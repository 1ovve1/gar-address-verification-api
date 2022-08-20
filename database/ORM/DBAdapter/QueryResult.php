<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

interface QueryResult
{
	public const PDO_F_ALL = \PDO::FETCH_ASSOC;
	public const PDO_F_COL = \PDO::FETCH_COLUMN;

	/**
	 * Fetching last query by special flag
	 *
	 * @param int $flag - fetching flag
	 * @return array<mixed>|false|null
	 */
	public function fetchAll(int $flag = \PDO::FETCH_ASSOC): array|false|null;
}