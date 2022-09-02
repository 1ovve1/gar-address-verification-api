<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;

use DB\Exceptions\NullableQueryResultException;
use DB\ORM\DBAdapter\QueryResult;
use PDOStatement;

class PDOQueryResult implements QueryResult
{
	/**
	 * @var array<int, array<int|string, mixed>>
	 */
	private ?array $fetchResult = null;

	function __construct(
		private readonly ?PDOStatement $queryState
	)
	{}

	/**
	 * @inheritDoc
	 */
	public function fetchAll(int $flag = QueryResult::PDO_F_ASSOC): array|false
	{
		if (null === $this->fetchResult) {
			try {
				$this->fetchResult = $this->getQueryResult()->fetchAll($flag);
			} catch (NullableQueryResultException) {
				return false;
			}
		}

		return $this->fetchResult;
	}

	/**
	 * @inheritDoc
	 */
	function rowCount(): int
	{
		try{
			return $this->getQueryResult()->rowCount();
		} catch (NullableQueryResultException) {
			return 0;
		}
	}

	/**
	 * @inheritDoc
	 */
	function isEmpty(): bool
	{
		return empty($this->rowCount());
	}

	/**
	 * @inheritDoc
	 */
	function hasManyRows(): bool
	{
		return $this->rowCount() > 1;
	}

	/**
	 * @return PDOStatement
	 * @throws NullableQueryResultException
	 */
	function getQueryResult(): PDOStatement
	{
		if (null === $this->queryState) {
			throw new NullableQueryResultException('nullable query result');
		}

		return $this->queryState;
	}
}