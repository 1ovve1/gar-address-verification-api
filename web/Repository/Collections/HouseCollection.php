<?php declare(strict_types=1);

namespace GAR\Repository\Collections;

use GAR\Repository\Elements\HouseElement;
use RuntimeException;

class HouseCollection extends BaseCollection
{
	/**
	 * @param array<HouseElement> $houseElements
	 */
	function __construct(
		array $houseElements
	)
	{
		parent::__construct($houseElements);
	}

	/**
	 * @param array<mixed> $queryResult
	 * @return self
	 * @throws RuntimeException
	 */
	static function fromQueryResult(array $queryResult): Collection
	{
		$elementCollection = [];
		foreach ($queryResult as $queryElement) {
			$elementCollection[] = HouseElement::fromQueryResult($queryElement);
		}

		return new self($elementCollection);
	}

}