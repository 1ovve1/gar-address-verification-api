<?php declare(strict_types=1);

namespace GAR\Storage\Builders;

use DB\ORM\DBAdapter\QueryResult;

interface AddressBuilder
{
	/**
	 * @param QueryResult $data
	 * @param ItemTypes $type
	 * @param ?string $rawName
	 * @return AddressBuilder
	 */
	function addItemsUpper(QueryResult $data, ItemTypes $type, ?string $rawName = null): self;

	/**
	 * @param QueryResult $data
	 * @param ItemTypes $type
	 * @param ?string $rawName
	 * @return AddressBuilder
	 */
	function addItemsDown(QueryResult $data, ItemTypes $type, ?string $rawName = null): self;

	/**
	 * Return complete address structure
	 * @return array<int, array{
	 *     raw: string|null,
	 *     type: string,
	 *     items: AddressElementContract,
	 *  }>
	 */
	function getAddress(): array;

	/**
	 * @param int $downIndex
	 * @param int $upperIndex
	 * @return void
	 */
	function resetAndReshape(int $downIndex, int $upperIndex): void;
}