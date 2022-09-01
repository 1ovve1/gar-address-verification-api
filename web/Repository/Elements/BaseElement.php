<?php declare(strict_types=1);

namespace GAR\Repository\Elements;

use RuntimeException;

abstract class BaseElement implements Element
{
	/**
	 * @param array<mixed> $data
	 */
	function __construct(
		private readonly array $data
	)
	{}

	/**
	 * @return array<mixed>
	 */
	function getData(): array
	{
		return $this->data;
	}
}