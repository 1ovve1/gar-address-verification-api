<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;

use DB\ORM\DBAdapter\QueryResult;
use PDO;
use PDOStatement;

class PDOQueryResult implements QueryResult
{


	function __construct(
		private readonly ?PDOStatement $queryState
	)
	{}

	/**
	 * @inheritDoc
	 */
	public function fetchAll(int $flag = PDO::FETCH_ASSOC): array|false|null
	{
		return $this->queryState?->fetchAll($flag);
	}

}