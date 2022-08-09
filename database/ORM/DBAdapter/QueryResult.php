<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

interface QueryResult
{
	/**
	 * Fetching last query by special flag
	 *
	 * @param  int $flag - fetching flag
	 * @return mixed
	 */
	public function fetchAll(int $flag = \PDO::FETCH_ASSOC): mixed;
}