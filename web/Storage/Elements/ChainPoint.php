<?php declare(strict_types=1);

namespace GAR\Storage\Elements;

use DB\ORM\DBAdapter\QueryResult;

class ChainPoint
{
	/**
	 * Create chain element
	 * @param int $parentObjectId
	 * @param int $parentPosition - position of parent element's objectid in user input
	 * @param int $chiledObjectId
	 * @param int $chiledPosition - position of chiled element's objectid in user input
	 */
	public function __construct(
		public readonly int $parentObjectId,
		public readonly int $parentPosition,
		public readonly int $chiledObjectId,
		public readonly int $chiledPosition
	)
	{}

	/**
	 * @param QueryResult $queryResult
	 * @param int $parentPosition
	 * @param int $chiledPosition
	 * @return self
	 */
	static function fromQueryResult(QueryResult $queryResult, int $parentPosition, int $chiledPosition): self
	{
		[[$parentObjectId, $chiledObjectId]] = $queryResult->fetchAllNum();

		return new self((int)$parentObjectId, $parentPosition, (int)$chiledObjectId, $chiledPosition);
	}

}