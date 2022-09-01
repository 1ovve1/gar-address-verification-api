<?php declare(strict_types=1);

namespace GAR\Repository\Collections;

use GAR\Repository\Elements\Element;

abstract class BaseCollection implements Collection
{
	/**
	 * @param array<Element> $elements
	 */
	function __construct(
		private readonly array $elements
	) {}

	/**
	 * @inheritDoc
	 */
	function getCollection(): array
	{
		return $this->elements;
	}

	/**
	 * @inheritDoc
	 */
	function toArray(): array
	{
		$data = [];

		foreach ($this->getCollection() as $element) {
			$data[] = $element->getData();
		}

		return $data;
	}

	/**
	 * @inheritDoc
	 */
	function isContainsOnlyOneElement(): bool
	{
		return count($this->elements) === 1;
	}

	/**
	 * @param string $param
	 * @return mixed
	 */
	function tryFinedFirstParam(string $param): mixed
	{
		foreach ($this->getCollection() as $element) {
			$data = $element->getData();

			if (key_exists($param, $data)) {
				return $data[$param];
			}
		}

		return null;
	}


	/**
	 * @inheritDoc
	 */
	function isNotEmpty(): bool
	{
		return !empty($this->elements);
	}

	/**
	 * @return bool
	 */
	function hasMany(): bool
	{
		return count($this->elements) >= 2;
	}
}