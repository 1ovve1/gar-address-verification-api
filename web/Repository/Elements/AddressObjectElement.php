<?php declare(strict_types=1);

namespace GAR\Repository\Elements;

use RuntimeException;

class AddressObjectElement extends BaseElement
{
	/**
	 * Create address element from query result array
	 * Return null if query result have isn't compatible format
	 * @param array<mixed> $queryResult
	 * @return AddressObjectElement
	 * @throws RuntimeException
	 */
	static function fromQueryResult(array $queryResult): self
	{
		if (empty($queryResult)) {
			throw new RuntimeException('empty query result');
		}

		return new self($queryResult);
	}
}