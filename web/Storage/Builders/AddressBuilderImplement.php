<?php declare(strict_types=1);

namespace GAR\Storage\Builders;

class AddressBuilderImplement implements AddressBuilder
{
	/** @var array<string, array<string|int, mixed>> */
	private array $address = [];

	/**
	 * @inheritDoc
	 */
	function addParentAddr(string $identifier, array $data): AddressBuilder
	{
		array_unshift($this->address, [$identifier => $data]);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	function addChiledAddr(string $identifier, array $data): AddressBuilder
	{
		$this->address[] = [$identifier => $data];

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	function addChiledHouses(array $data): AddressBuilder
	{
		$this->address[] = ['houses' => $data];

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	function addChiledVariant(array $data): AddressBuilder
	{
		$this->address[] = ['variants' => $data];

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	function getAddress(): array
	{
		return $this->address;
	}
}