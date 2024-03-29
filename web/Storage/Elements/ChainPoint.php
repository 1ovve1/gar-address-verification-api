<?php declare(strict_types=1);

namespace GAR\Storage\Elements;

use QueryBox\DBAdapter\QueryResult;

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
		/**
		 * @var int $parentObjectId
		 * @var int $chiledObjectId
		 */
		[['parentobjid_addr' => $parentObjectId, 'chiledobjid_addr' => $chiledObjectId]] = $queryResult->fetchAllAssoc();

		return new self($parentObjectId, $parentPosition, $chiledObjectId, $chiledPosition);
	}

}