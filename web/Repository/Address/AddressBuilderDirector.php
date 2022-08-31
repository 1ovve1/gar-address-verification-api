<?php declare(strict_types=1);

namespace GAR\Repository\Address;

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
	/** @var bool $variantStatus - indicate that chain ends with 'variant' field*/
	private bool $variantsStatus = false;

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
	 * @param array<string, mixed> $data
	 * @return AddressBuilderDirector
	 */
	function addParentAddr(array $data): self
	{
		$identifier = match($this->isParentPosNotOverflow()) {
			true => $this->getCurrentParentName(),
			false => "parent_" . (1 - $this->parentPos)
		};

		$this->addressBuilder->addParentAddr($identifier, $data);
		$this->parentPos--;

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
	 * @param array<string, mixed> $data
	 * @return AddressBuilderDirector
	 */
	function addChiledAddr(array $data): self
	{
		$identifier = match($this->isChiledPosNotOverflow()) {
			true => $this->getCurrentChiledName(),
			false => throw new RuntimeException('parent position are max')
		};

		$this->addressBuilder->addChiledAddr($identifier, $data);
		$this->chiledPos++;

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
	 * @param array<string, mixed> $data
	 * @return AddressBuilderDirector
	 */
	function addChiledHouses(array $data): self
	{
		$this->addressBuilder->addChiledHouses($data);
		return $this;
	}

	/**
	 * @param array<string|int, mixed> $data
	 * @return AddressBuilderDirector
	 */
	function addChiledVariant(array $data): self
	{
		$this->addressBuilder->addChiledVariant($data);
		$this->variantsStatus = true;
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
	 * @return string - current chield name
	 */ 
	function getCurrentChiledName(): string
	{
		return $this->userAddress[$this->chiledPos];
	}

	/**
	 * @return string - current child name
	 */ 
	function getCurrentParentName(): string
	{
		return $this->userAddress[$this->parentPos];
	}

	/**
	 * @return bool
	 */ 
	function isChainEndsByVariant(): bool
	{
		return $this->variantsStatus;
	}
}