<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

interface QueryResult
{
	/**
	 * Fetching last query by special flag
	 *
	 * @param int $flag - fetching flag
	 * @return array<mixed>|false|null
	 */
	public function fetchAll(int $flag = \PDO::FETCH_ASSOC): array|false|null;
}