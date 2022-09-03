<?php declare(strict_types=1);

namespace Tests\Mock;

use DB\ORM\DBAdapter\QueryResult;
use RuntimeException;

class FakeQueryResult implements QueryResult
{
	readonly array $assocFetch;
	readonly array $numFetch;
	readonly int $size;

	public function __construct(array $data)
	{
		$this->assocFetch = $data;

		$numData = [];
		foreach ($data as $element) {
			$tmpElement = [];
			foreach ($element as $attribute) {
				if (is_array($attribute)) {
					throw new RuntimeException('Bad format QueryResult');
				}
				$tmpElement[] = $attribute;
			}
			$numData[] = $tmpElement;
		}

		$this->numFetch = $numData;

		$this->size = count($numData);
	}

	/**
	 * @param int $flag
	 * @return array|false
	 */
	function fetchAll(int $flag = \PDO::FETCH_ASSOC): array|false
	{
		return $this->assocFetch + $this->numFetch;
	}

	/**
	 * @return array|false
	 */
	function fetchAllAssoc(): array|false
	{
		return $this->assocFetch;
	}

	/**
	 * @return array|false|mixed[]
	 */
	function fetchAllNum(): array|false
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