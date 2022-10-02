<?php

declare(strict_types=1);

namespace GAR\Storage;

use GAR\Exceptions\Checked\AddressNotFoundException;
use GAR\Exceptions\Checked\ChainNotFoundException;
use GAR\Exceptions\Checked\ParamNotFoundException;
use GAR\Storage\Builders\AddressBuilder;
use GAR\Storage\Builders\AddressBuilderDirector;
use GAR\Storage\Elements\ChainPoint;
use RuntimeException;


/**
 * Repo that contains methods that use for get full name of address by specific name address
 */
class AddressByNameStorage extends BaseStorage
{
	const SINGLE_WORD = 1;
	const DOUBLE_WORD = 2;

	/** @var AddressBuilder $addressBuilder - address builder */
	protected AddressBuilder $addressBuilder;
	protected AddressBuilderDirector $addressBuilderDirector;

	public function __construct(AddressBuilder $addressBuilder)
	{
		$this->addressBuilder = $addressBuilder;
		parent::__construct();
	}

	/**
	 * @param array<string> $userAddress
	 * @param int $region
	 * @return int
	 * @throws AddressNotFoundException
	 */
	function getChiledObjectIdFromAddress(array $userAddress, int $region): int
	{
		$this->getFullAddress($userAddress, $region);

		try {
			return $this->addressBuilderDirector->findChiledObjectId();
		} catch (ParamNotFoundException) {
			throw new AddressNotFoundException();
		}
	}

	/**
	 * Return full address by fragment of $halfAddress
	 * @param array<string> $userAddress - exploded input address fragment
	 * @param int $region - region context
	 * @return array<int, array<string, AddressElementContract>> - full address
	 * @throws AddressNotFoundException - address was not found
	 */
    function getFullAddress(array $userAddress, int $region): array
    {
		$this->setRegionContext($region);
		$this->addressBuilderDirector = new AddressBuilderDirector($this->addressBuilder, $userAddress);

		switch(count($userAddress)) {
			case self::SINGLE_WORD:
				$this->handleSingleWordUserAddress();
				break;
			case self::DOUBLE_WORD:
				$this->handleDoubleWordUserAddress();
				break;
			default:
				$this->handleComplexUserAddress();

		}

		$address = $this->addressBuilder->getAddress();

		if (empty($address)) {
			throw new AddressNotFoundException();
		}

        return $address;
    }

	/**
	 * @return void
	 */
	protected function handleSingleWordUserAddress(): void
	{
		$word = $this->addressBuilderDirector->getCurrentChiledName();

		$this->completeAddressChainBackward($word);
	}

	/**
	 * @return void
	 */
	function handleDoubleWordUserAddress(): void
	{
		$parentName = $this->addressBuilderDirector->getCurrentParentName();

		$this->completeAddressChainBackward($parentName);

		try {
			$parentObjectId = $this->addressBuilderDirector->findObjectIdFromIdentifier($parentName);
		} catch (ParamNotFoundException) {
			// it is mean that after completeAddressChainBackward we
			// get an address with variants identifier
			// (addressBuilder didn't have an identifier like $parentName)
			return;
		}

		$currChiledName = $this->addressBuilderDirector->getCurrentChiledName();

		$chiledLikeAddress = $this->db->getChiledAddressByParentObjectIdAndChiledAddressName($parentObjectId, $currChiledName, $this->getRegionContext());

		if ($chiledLikeAddress->hasOnlyOneRow()) {
			$this->addressBuilderDirector->addChiledAddr($chiledLikeAddress);
		} elseif ($chiledLikeAddress->isNotEmpty()) {
			$this->addressBuilderDirector->addChiledVariant($chiledLikeAddress);
		}
	}

	/**
	 * @return void
	 */
	protected function handleComplexUserAddress(): void
	{
		$userAddress = $this->addressBuilderDirector->getUserAddress();

		try {
			$chain = $this->findSimilarAddressChain($userAddress);
		} catch (ChainNotFoundException) {
			return;
		}

		$this->addressBuilderDirector = AddressBuilderDirector::fromChainPoint($this->addressBuilderDirector, $chain);

        $this->completeAddressChainBackward($chain->parentObjectId);
        $this->completeAddressChainForward($chain->chiledObjectId);
    }


	/**
	 * @param array<string> $userAddress
	 * @return ChainPoint
	 * @throws ChainNotFoundException
	 */
	protected function findSimilarAddressChain(array $userAddress): ChainPoint
	{
		$userAddressLength = count($userAddress);

		for ($parent = 0, $chiled = 1; $chiled < $userAddressLength; ++$parent, ++$chiled) {
			$parentNameCurrChain = $userAddress[$parent];
			$chiledNameCurrChain = $userAddress[$chiled];

			$chainObjectId = $this->db->findChainByParentAndChiledAddressName($parentNameCurrChain, $chiledNameCurrChain, $this->getRegionContext());

			// check if chain is single value
			if ($chainObjectId->hasOnlyOneRow()) {
				// if it true we return chain element object

                return ChainPoint::fromQueryResult($chainObjectId, $parent, $chiled);
			}
		}

		// if chin was not found we throw an exception
		throw new ChainNotFoundException();
	}

	/**
	 * @param int|string $currObjectIdOrName
	 */
    protected function completeAddressChainBackward(int|string $currObjectIdOrName): void
    {
	    $parentAddress = match(is_string($currObjectIdOrName)) {
			true => $this->db->getLikeAddress($currObjectIdOrName, $this->getRegionContext()),
			false => $this->db->getAddressByObjectId($currObjectIdOrName, $this->getRegionContext()),
        };

	    while ($parentAddress->hasOnlyOneRow()) {
			$this->addressBuilderDirector->addParentAddr($parentAddress);

		    try {
			    $parentObjectId = $this->addressBuilderDirector->findParentObjectId();
		    } catch (ParamNotFoundException) {
				break;
		    }

		    $parentAddress = $this->db->getParentAddressByChiledObjectId($parentObjectId, $this->getRegionContext());
		}

		if ($parentAddress->hasManyRows()) {
			$this->addressBuilderDirector->addChiledVariant($parentAddress);
		}
    }

	/**
	 * @param int|string $currObjectIdOrName
	 */
    protected function completeAddressChainForward(int|string $currObjectIdOrName): void
    {
		$chiledAddress = match(is_string($currObjectIdOrName)) {
			true => $this->db->getLikeAddress($currObjectIdOrName, $this->getRegionContext()),
			false => $this->db->getAddressByObjectId($currObjectIdOrName, $this->getRegionContext())
		};

        while ($chiledAddress->hasOnlyOneRow())
        {
	        $this->addressBuilderDirector->addChiledAddr($chiledAddress);

			try {
				// look forward and try fined next chiled address name
				$nextChiledName = $this->addressBuilderDirector->getCurrentChiledName();
				// because we move forward actual chiled address use like a parent
				$parentObjectId = $this->addressBuilderDirector->findChiledObjectId();
			} catch (RuntimeException|ParamNotFoundException) {
				break;
			}

	        $chiledAddress = $this->db->getChiledAddressByParentObjectIdAndChiledAddressName($parentObjectId, $nextChiledName, $this->getRegionContext());
        }

		if ($chiledAddress->hasManyRows()) {
			$this->addressBuilderDirector->addChiledVariant($chiledAddress);
		} elseif(isset($parentObjectId)) {
			$houseAddress = $this->db->getHousesByParentObjectId($parentObjectId, $this->getRegionContext());

			if ($houseAddress->isNotEmpty()) {
				$this->addressBuilderDirector->addChiledHouses($houseAddress);
			}
		}
    }


}