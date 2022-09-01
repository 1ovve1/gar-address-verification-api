<?php declare(strict_types=1);

namespace GAR\Repository\Builders;

use GAR\Repository\Collections\AddressObjectCollection;
use GAR\Repository\Collections\HouseCollection;
use GAR\Repository\Elements\ChainPoint;
use RuntimeException;

class AddressBuilderDirector
{
	/** @var AddressBuilder $addressBuilder */
	private AddressBuilder $addressBuilder;
	/** @var array<string> $userAddress */
	private readonly array $userAddress;
	/** @var int $userAddressLength */
	private readonly int $userAddressLength;
	/** @var int $parentPos - position of parent address chain */
	private int $parentPos;
	/** @var int $chiledPos - position of chiled address chain */
	private int $chiledPos;

	/**
	 * @param AddressBuilder &$addressBuilder - builder ref
	 * @param array<string> $userAddress - user address
	 * @param int $parentPos - parent address element position
	 * @param int $chiledPos - chiled address element position
	 */
	function __construct(AddressBuilder &$addressBuilder, 
						 array $userAddress,
						 int $parentPos = 0,
						 int $chiledPos = 1)
	{
		$this->addressBuilder = $addressBuilder;
		$this->userAddress = $userAddress;
		$this->userAddressLength = count($userAddress);

		$this->posValidate($parentPos, $chiledPos);

		$this->parentPos = $parentPos;
		$this->chiledPos = $chiledPos;
	}

	/**
	 * @param AddressBuilder $addressBuilder
	 * @param array<string> $userAddress
	 * @param ChainPoint $chainElement
	 * @return self
	 */
	static function fromChainPoint(AddressBuilder &$addressBuilder, array $userAddress, ChainPoint $chainElement): self
	{
		return new self($addressBuilder, $userAddress, $chainElement->parentPosition, $chainElement->chiledPosition);
	}

	/**
	 * @param int $parentPos
	 * @param int $chiledPos
	 * @return void
	 * @throws RuntimeException 	 
	 */ 
	private function posValidate(int $parentPos, int $chiledPos): void
	{
		if ($parentPos >= $this->userAddressLength ||  
			$parentPos < 0 ||
			$chiledPos >= $this->userAddressLength ||
			$chiledPos < 0) {
			throw new RuntimeException(sprintf(
				"Out of range with pos '%s' and '%s' (total length is %s)",
				$parentPos, $chiledPos, $this->userAddressLength
			));
		} elseif ($parentPos >= $chiledPos) {
			throw new RuntimeException(sprintf(
				"parent pos '%s' should be lower than chiledPos, but chiled pos is '%s'",
				$parentPos, $chiledPos
			));
		}
	}

	/**
	 * @param AddressObjectCollection $addressObjectCollection
	 * @return AddressBuilderDirector
	 */
	function addParentAddr(AddressObjectCollection $addressObjectCollection): self
	{
		foreach ($addressObjectCollection->getCollection() as $addressElement) {

			$identifier = match($this->isParentPosNotOverflow()) {
				true => $this->getCurrentParentName(),
				false => "parent_" . (1 - $this->parentPos)
			};

			$this->addressBuilder->addParentAddr($identifier, $addressElement->getData());
			$this->parentPos--;
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	private function isParentPosNotOverflow(): bool
	{
		return $this->parentPos >= 0;
	}

	/**
	 * @param AddressObjectCollection $addressObjectCollection
	 * @return AddressBuilderDirector
	 */
	function addChiledAddr(AddressObjectCollection $addressObjectCollection): self
	{
		foreach ($addressObjectCollection->getCollection() as $addressElement) {

			$identifier = match($this->isChiledPosNotOverflow()) {
				true => $this->getCurrentChiledName(),
				false => throw new RuntimeException('chiled position are max')
			};

			$this->addressBuilder->addChiledAddr($identifier, $addressElement->getData());
			$this->chiledPos++;
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	function isChiledPosNotOverflow(): bool
	{
		return $this->chiledPos < $this->userAddressLength;
	}

	/**
	 * @param HouseCollection $houseCollection
	 * @return AddressBuilderDirector
	 */
	function addChiledHouses(HouseCollection $houseCollection): self
	{
		$this->addressBuilder->addChiledHouses($houseCollection->toArray());
		return $this;
	}

	/**
	 * @param AddressObjectCollection $addressObjectCollection
	 * @return AddressBuilderDirector
	 */
	function addChiledVariant(AddressObjectCollection $addressObjectCollection): self
	{
		$this->addressBuilder->addChiledVariant($addressObjectCollection->toArray());
		return $this;
	}

	/**
	 * Return complete address structure
	 * @return array<string, array<string|int, mixed>>
	 */
	function getAddress(): array
	{
		return $this->addressBuilder->getAddress();
	}

	/**
	 * @return string - current child name
	 */ 
	function getCurrentChiledName(): string
	{
		if (!$this->isChiledPosNotOverflow()) {
			throw new RuntimeException('chiled position overflow');
		}

		return $this->userAddress[$this->chiledPos];
	}

	/**
	 * check if next chiled name can be given
	 * @return bool
	 */
	function hasNextChiledName(): bool
	{
		return $this->userAddressLength < $this->chiledPos + 1;
	}

	/**
	 * @return string - current child name
	 */ 
	function getCurrentParentName(): string
	{
		return $this->userAddress[$this->parentPos];
	}

}