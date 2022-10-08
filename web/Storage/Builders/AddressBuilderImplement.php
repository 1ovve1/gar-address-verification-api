<?php declare(strict_types=1);

namespace GAR\Storage\Builders;

use DB\ORM\DBAdapter\QueryResult;

class AddressBuilderImplement implements AddressBuilder
{
	/** @var AddressJSON  $address */
	private array $address = [];
	private int $downIndex = 1;
	private int $upperIndex = 0;

	/**
	 * @param string|null $rawName
	 * @param ItemTypes $type
	 * @param QueryResult $data
	 * @return AddressJSON
	 */
	private static function getDataWrap(?string $rawName, ItemTypes $type, QueryResult $data): array
	{
		return [
			'raw' => $rawName,
			'type' => $type->value,
			'items' => $data->fetchAllAssoc()
		];
	}

	/**
	 * @inheritDoc
	 */
	function addItemsUpper(QueryResult $data, ItemTypes $type, ?string $rawName = null): AddressBuilder
	{
		$data = self::getDataWrap($rawName, $type, $data);

		$this->address[$this->upperIndex--] = $data;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	function addItemsDown(QueryResult $data, ItemTypes $type, ?string $rawName = null): AddressBuilder
	{
		$data = self::getDataWrap($rawName, $type, $data);

		$this->address[$this->downIndex++] = $data;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	function getAddress(): array
	{
		return $this->address;
	}

	/**
	 * @inheritDoc
	 */
	function resetAndReshape(int $downIndex, int $upperIndex): void
	{
		$this->address = [];
		$this->downIndex = $downIndex;
		$this->upperIndex = $upperIndex;
	}


}