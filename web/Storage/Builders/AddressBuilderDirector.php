<?php declare(strict_types=1);

namespace GAR\Storage\Builders;

use DB\ORM\DBAdapter\QueryResult;
use GAR\Exceptions\Checked\ParamNotFoundException;
use GAR\Storage\Elements\ChainPoint;
use RuntimeException;

class AddressBuilderDirector
{
	/** @var AddressBuilder  */
	readonly AddressBuilder $addressBuilder;
	/** @var String[]  */
	readonly array $userAddress;
	/** @var int  */
	readonly int $startChiledPoint;
	/** @var int  */
	readonly int $startParentPoint;
	/** @var int  */
	private int $chiledCursor;
	/** @var int  */
	private int $parentCursor;
	/** @var bool */
	private bool $finalFlag = false;

	/**
	 * @param AddressBuilder $addressBuilder
	 * @param String[] $userAddress
	 * @param int $startChiledPoint
	 * @param int $startParentPoint
	 */
	public function __construct(AddressBuilder $addressBuilder, array $userAddress,
	                            int $startChiledPoint = 1, int $startParentPoint = 0)
	{
		$this->addressBuilder = $addressBuilder;
		$this->userAddress = $userAddress;
		$this->chiledCursor = $this->startChiledPoint = $startChiledPoint;
		$this->parentCursor = $this->startParentPoint = $startParentPoint;
	}

	/**
	 * @param AddressBuilder $addressBuilder
	 * @param String[] $userAddress
	 * @param ChainPoint $chain
	 * @return self
	 */
	static function fromChainPoint(AddressBuilder $addressBuilder, array $userAddress, ChainPoint $chain): self
	{
		$addressBuilder->resetAndReshape($chain->chiledPosition, $chain->parentPosition);
		return new self($addressBuilder, $userAddress, $chain->chiledPosition, $chain->parentPosition);
	}



	/**
	 * @param QueryResult $data
	 * @return void
	 * @throws ParamNotFoundException
	 */
	function addParentAddress(QueryResult $data): void
	{
		$rawName = $this->getCurrentRawParentName();

		$this->addressBuilder->addItemsUpper($data, ItemTypes::ITEM, $rawName);
		$this->parentCursor--;
	}

	/**
	 * @return string
	 * @throws ParamNotFoundException
	 */
	function getCurrentRawParentName(): string
	{
		return $this->userAddress[$this->parentCursor]
			?? throw new ParamNotFoundException('parent name from address builder', $this);
	}

	/**
	 * @param QueryResult $data
	 * @return void
	 * @throws ParamNotFoundException
	 */
	function addChiledAddress(QueryResult $data): void
	{
		$rawName = $this->getCurrentRawChiledName();

		$this->addressBuilder->addItemsDown($data, ItemTypes::ITEM, $rawName);
		$this->chiledCursor++;
	}

	/**
	 * @return string
	 * @throws ParamNotFoundException
	 */
	function getCurrentRawChiledName(): string
	{
		return $this->userAddress[$this->chiledCursor]
			?? throw new ParamNotFoundException('chiled name from address builder', $this);
	}

	/**
	 * @param QueryResult $data
	 * @return void
	 */
	function addHouses(QueryResult $data): void
	{
		$this->abortIfAddressFinalOrMakeItFinal();
		$this->addressBuilder->addItemsDown($data, ItemTypes::HOUSES);
	}

	/**
	 * @param QueryResult $data
	 * @return void
	 */
	function addVariant(QueryResult $data): void
	{
		$this->abortIfAddressFinalOrMakeItFinal();
		$this->addressBuilder->addItemsDown($data, ItemTypes::VARIANT);
	}

	/**
	 * Panic if we call addHouse or addVariant twice
	 * @return void
	 */
	private function abortIfAddressFinalOrMakeItFinal(): void
	{
		if (false === $this->finalFlag) {
			$this->finalFlag = true;
		} else {
			throw new RuntimeException('Try to add another item in final structure: ' . print_r($this, true));
		}
	}

	/**
	 * Add parent address that was not notice by user, but his contains in address chain
	 * @param QueryResult $data
	 * @return void
	 */
	function addUnknownParentAddress(QueryResult $data): void
	{
		$this->addressBuilder->addItemsUpper($data, ItemTypes::PARENT);
		$this->parentCursor--;
	}

	/**
	 * @return int
	 * @throws ParamNotFoundException
	 */
	function getChiledObjectIdFromEnd(): int
	{
		/** @var int $objectId */
		$objectId = $this->getParamFromItem('objectid', $this->getPrevChiledPoint());
		return $objectId;
	}

	/**
	 * @return int
	 * @throws ParamNotFoundException
	 */
	function getChiledObjectIdFromStart(): int
	{
		/** @var int $objectId */
		$objectId = $this->getParamFromItem('objectid', $this->startChiledPoint);
		return $objectId;
	}

	/**
	 * @return int
	 * @throws ParamNotFoundException
	 */
	function getParentObjectIdFromEnd(): int
	{
		/** @var int $objectId */
		$objectId = $this->getParamFromItem('objectid', $this->getPrevParentPoint());
		return $objectId;
	}

	/**
	 * @return int
	 * @throws ParamNotFoundException
	 */
	function getParentObjectIdFromStart(): int
	{
		/** @var int $objectId */
		$objectId = $this->getParamFromItem('objectid', $this->startParentPoint);
		return $objectId;
	}

	/**
	 * Return previous chiled point (chiledPoint - 1)
	 * @return int
	 * @throws ParamNotFoundException
	 */
	private function getPrevChiledPoint(): int
	{
		return match($this->chiledCursor > $this->startChiledPoint) {
			true => $this->chiledCursor - 1,
			default => throw new ParamNotFoundException("Chiled point overflow", print_r($this, true))
		};
	}

	/**
	 * Return previous parent point (parentPoint + 1)
	 * @return int
	 * @throws ParamNotFoundException
	 */
	private function getPrevParentPoint(): int
	{
		return match($this->parentCursor < $this->startParentPoint) {
			true => $this->parentCursor + 1,
			default => throw new ParamNotFoundException("Chiled point overflow", print_r($this, true))
		};
	}

	/**
	 * @param string $paramName
	 * @param int $itemCollectionIndex
	 * @return mixed
	 * @throws ParamNotFoundException
	 */
	private function getParamFromItem(string $paramName, int $itemCollectionIndex): mixed
	{
		$addressCollection = $this->addressBuilder->getAddress();
		if (isset($addressCollection[$itemCollectionIndex])) {
			$itemCollection = $addressCollection[$itemCollectionIndex];
			$type = ItemTypes::tryFrom($itemCollection['type']);

			if (null !== $type && $type !== ItemTypes::VARIANT && $type !== ItemTypes::HOUSES) {
				[$item] = $itemCollection['items'];

				if (isset($item[$paramName])) {
					$objectId = $item['objectid'];
				} else {
					throw new ParamNotFoundException("Param '{$paramName}' was not found", print_r($item, true));
				}
			} else {
				throw new ParamNotFoundException("Unsupported item collection type", print_r($type, true));
			}
		} else {
			throw new ParamNotFoundException(
				"Item collection are not exists by index '{$itemCollectionIndex}'", print_r($addressCollection, true)
			);
		}

		return $objectId;
	}

	/**
	 * Check if address if final
	 * @return bool
	 */
	function isFinal(): bool
	{
		return $this->finalFlag;
	}
}