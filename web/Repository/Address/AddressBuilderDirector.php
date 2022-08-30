<?php declare(strict_types=1);

namespace GAR\Repository\Address;

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
	 * @param AddressBuilder &$addressBuilder
	 * @param array<string> $userAddress
	 * @throws RuntimeException - see posValidate
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
			throw new \RuntimeException("Out of range with pos '{$parentPos}' and '{$chiledPos}' (total length is {$addressLength}");
		} elseif ($parentPos >= $chiledPos) {
			throw new \RuntimeException("parent pos '{$parentPos}' should be lower than chiledPos, but chiled pos is '{$chiledPos}'");
		}
	}

	/**
	 * @param array<string, mixed> $data
	 * @return AddressBuilder
	 * @throws RuntimeException
	 */
	function addParentAddr(array $data): self
	{
		if ($this->isParentPosNotOverflow()) {
			$identifire = $this->getCurrentParentName();

		} else {
			$identifire = "parent_" . 1 - $this->parentPos;

		}

		$this->addressBuilder->addParentAddr($identifire, $data);	
		$this->parentPos--;	

		return $this;
	}

	/**
	 * @return void
	 */ 
	private function isParentPosNotOverflow(): bool
	{
		return $this->parentPos >= 0;
	}

	/**
	 * @param array<string, mixed> $data
	 * @return AddressBuilder
	 * @throws RuntimeException
	 */
	function addChiledAddr(array $data): self
	{
		if ($this->isChiledPosNotOverflow()) {
			$identifire = $this->getCurrentChiledName();

		} else {
			throw new \RuntimeException('parent position are max');
		}

		$this->addressBuilder->addChiledAddr($identifire, $data);
		$this->chiledPos++;

		return $this;
	}

	/**
	 * @return void
	 */ 
	function isChiledPosNotOverflow(): bool
	{
		return $this->chiledPos < $this->userAddressLength;
	}

	/**
	 * @param array<string, mixed> $data
	 * @return AddressBuilder
	 */
	function addChiledHouses(array $data): self
	{
		$this->addressBuilder->addChiledHouses($data);
		return $this;
	}

	/**
	 * @param array<string|int, mixed> $data
	 * @return AddressBuilder
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
		$this->addressBuilder->getAddress();
	}

	/**
	 * @return string - current chield name
	 */ 
	function getCurrentChiledName(): string
	{
		return $this->userAddress[$this->chiledPos];
	}

	/**
	 * @return string - current chield name
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