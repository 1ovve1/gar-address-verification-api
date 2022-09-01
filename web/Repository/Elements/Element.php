<?php declare(strict_types=1);

namespace GAR\Repository\Elements;

use RuntimeException;

interface Element
{
	/**
	 * @return array<mixed>
	 */
	function getData(): array;

	/**
	 * @param array<mixed> $queryResult
	 * @return Element
	 */
	static function fromQueryResult(array $queryResult): Element;
}