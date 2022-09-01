<?php declare(strict_types=1);

namespace GAR\Repository\Collections;

use GAR\Repository\Elements\Element;

interface Collection
{
	/**
	 * @param array<mixed> $queryResult
	 * @return self
	 */
	static function fromQueryResult(array $queryResult): self;

	/**
	 * @return array<mixed>
	 */
	function toArray(): array;

	/**
	 * @return array<Element>
	 */
	function getCollection(): array;

	/**
	 * @return bool
	 */
	function isNotEmpty(): bool;

	/**
	 * @return bool
	 */
	function isContainsOnlyOneElement(): bool;

	/**
	 * @return bool
	 */
	function hasMany(): bool;

	/**
	 * Return first found param from collection list (better use after isContainsOnlyOneElement)
	 * @param string $param
	 * @return mixed
	 */
	function tryFinedFirstParam(string $param): mixed;
}