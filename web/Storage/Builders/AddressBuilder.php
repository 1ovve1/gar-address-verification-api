<?php declare(strict_types=1);

namespace GAR\Storage\Builders;

interface AddressBuilder
{
	/**
	 * @param string $identifier
	 * @param AddressElementContract $data
	 * @return AddressBuilder
	 */
	function addParentAddr(string $identifier, array $data): self;

	/**
	 * @param string $identifier
	 * @param AddressElementContract $data
	 * @return AddressBuilder
	 */
	function addChiledAddr(string $identifier, array $data): self;

	/**
	 * @param AddressElementContract $data
	 * @return AddressBuilder
	 */
	function addChiledHouses(array $data): self;

	/**
	 * @param AddressElementContract $data
	 * @return AddressBuilder
	 */
	function addChiledVariant(array $data): self;

	/**
	 * Return complete address structure
	 * @return array<int, array<string, AddressElementContract>>
	 */
	function getAddress(): array;
}