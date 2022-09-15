<?php declare(strict_types=1);

namespace Tests\Mock;

use DB\ORM\DBAdapter\QueryResult;
use RuntimeException;

class FakeQueryResult implements QueryResult
{
	/** @var array<int, array<string|int, DatabaseContract>> */
	readonly array $assocFetch;
	/** @var array<int, array<string|int, DatabaseContract>> */
	readonly array $numFetch;
	/** @var int  */
	readonly int $size;

	/**
	 * @param array<int, array<int|string, DatabaseContract>> $data
	 */
	public function __construct(array $data)
	{
		$this->assocFetch = $data;

		$numData = [];
		foreach ($data as $element) {
			$tmpElement = [];
			foreach ($element as $attribute) {
				$tmpElement[] = $attribute;
			}
			$numData[] = $tmpElement;
		}

		$this->numFetch = $numData;

		$this->size = count($numData);
	}

	/**
	 * @param int $flag
	 * @return array<int, array<int|string, DatabaseContract>>
	 */
	function fetchAll(int $flag = \PDO::FETCH_ASSOC): array
	{
		return $this->assocFetch + $this->numFetch;
	}

	/**
	 * @return array<int, array<string|int, DatabaseContract>>
	 */
	function fetchAllAssoc(): array
	{
		return $this->assocFetch;
	}

	/**
	 * @return array<int, array<string|int, DatabaseContract>>
	 */
	function fetchAllNum(): array
	{
		return $this->numFetch;
	}

	/**
	 * @return int
	 */
	function rowCount(): int
	{
		return $this->size;
	}

	/**
	 * @return bool
	 */
	function isEmpty(): bool
	{
		return empty($this->rowCount());
	}

	/**
	 * @return bool
	 */
	function isNotEmpty(): bool
	{
		return !$this->isEmpty();
	}

	/**
	 * @return bool
	 */
	function hasOnlyOneRow(): bool
	{
		return $this->size === 1;
	}

	/**
	 * @return bool
	 */
	function hasManyRows(): bool
	{
		return !$this->hasOnlyOneRow() && $this->isNotEmpty();
	}

}