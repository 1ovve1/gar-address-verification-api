<?php declare(strict_types=1);

namespace GAR\Repository\Elements;

use RuntimeException;

class HouseElement extends BaseElement
{
	/**
	 *
	 * @param array<mixed> $queryResult
	 * @return self
	 */
	static function fromQueryResult(array $queryResult): self
	{
		if (empty($queryResult)) {
			throw new RuntimeException('empty query result given');
		}

		return new self($queryResult);
	}
}