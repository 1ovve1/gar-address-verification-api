<?php declare(strict_types=1);

namespace GAR\Repository\Address;

interface AddressBuilder
{
	/**
	 * @param string $identifier
	 * @param array<string, mixed> $data
	 * @return AddressBuilder
	 */
	function addParentAddr(string $identifier, array $data): self;

	/**
	 * @param string $identifier
	 * @param array<string, mixed> $data
	 * @return AddressBuilder
	 */
	function addChiledAddr(string $identifier, array $data): self;

	/**
	 * @param array<string, mixed> $data
	 * @return AddressBuilder
	 */
	function addChiledHouses(array $data): self;

	/**
	 * @param array<string|int, mixed> $data
	 * @return AddressBuilder
	 */
	function addChiledVariant(array $data): self;

	/**
	 * Return complete address structure
	 * @return array<string, array<string|int, mixed>>
	 */
	function getAddress(): array;
}