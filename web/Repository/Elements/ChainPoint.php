<?php declare(strict_types=1);

namespace GAR\Repository\Elements;

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
	 * @param array{int, int} $result
	 * @param int $chiledPosition
	 * @param int $parentPosition
	 * @return self
	 */
	static function fromQueryResult(array $result, int $parentPosition, int $chiledPosition): self
	{
		[$parentObjectId, $chiledObjectId] = $result;

		return new self($parentObjectId, $parentPosition, $chiledObjectId, $chiledPosition);
	}

}