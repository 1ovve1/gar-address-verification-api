<?php declare(strict_types=1);

namespace GAR\Repository\Collections;

use GAR\Repository\Elements\AddressObjectElement;
use GAR\Repository\Elements\Element;
use RuntimeException;

class AddressObjectCollection extends BaseCollection
{
	/**
	 * @param array<Element> $addressElements
	 */
	public function __construct(array $addressElements)
	{
		parent::__construct($addressElements);
	}

	/**
	 * Create address element collection or return null if format isn't compatible (query result is empty)
	 * @param array<mixed> $queryResult
	 * @return self
	 * @throws RuntimeException
	 */
	static function fromQueryResult(array $queryResult): Collection
	{
		$collection = [];
		foreach ($queryResult as $resultElement) {
			try{
				$collection[] = AddressObjectElement::fromQueryResult($resultElement);
			} catch (RuntimeException $e) {
				throw new RuntimeException('unknown address element format');
			}
		}

		return new self($collection);
	}
}