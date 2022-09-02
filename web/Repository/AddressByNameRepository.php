<?php

declare(strict_types=1);

namespace GAR\Repository;

use GAR\Repository\Builders\AddressBuilder;
use GAR\Repository\Builders\AddressBuilderDirector;
use GAR\Repository\Builders\AddressBuilderImplement;
use GAR\Repository\Collections\AddressObjectCollection;
use GAR\Repository\Collections\HouseCollection;
use GAR\Repository\Elements\ChainPoint;
use RuntimeException;


/**
 * Repo that contains methods that use for get full name of address by specific name address
 */
class AddressByNameRepository extends BaseRepo
{
	const SINGLE_WORD = 1;
	const DOUBLE_WORD = 2;

	/** @var AddressBuilder $addressBuilder - address builder */
	protected AddressBuilder $addressBuilder;

	public function __construct(AddressBuilder $addressBuilder)
	{
		$this->addressBuilder = $addressBuilder;
		parent::__construct();
	}

	/**
     * Return full address by fragment of $halfAddress
     * @param  array<string>  $userAddress - exploded input address fragment
     * @return array<int, array<string, array<int, array<string, string|int>>>> - full address
     */
    public function getFullAddress(array $userAddress): array
    {
		switch(count($userAddress)) {
			case self::SINGLE_WORD:
				$this->handleSingleWordUserAddress(current($userAddress));
				break;
			case self::DOUBLE_WORD:
				$this->handleDoubleWordUserAddress($userAddress);
				break;
			default:
				$this->handleComplexUserAddress($userAddress);

		}

        return $this->addressBuilder->getAddress();
    }

	/**
	 * @param string $addressName
	 * @return void
	 */
	protected function handleSingleWordUserAddress(string $addressName): void
	{
		$checkLikeAddress = $this->db->getLikeAddress($addressName);

		if (!empty($checkLikeAddress)) {
			$this->addressBuilder->addChiledVariant($checkLikeAddress);
		}
	}

	function handleDoubleWordUserAddress(array $userAddress): void
	{
		$addressBuilderDirector = new AddressBuilderDirector($this->addressBuilder, $userAddress);

		$addressObjectCollection = AddressObjectCollection::fromQueryResult(
			$this->db->getLikeAddress($addressBuilderDirector->getCurrentParentName())
		);


		if ($addressObjectCollection->isContainsOnlyOneElement()) {
			$addressBuilderDirector->addParentAddr($addressObjectCollection);
			$objectId = $addressObjectCollection->tryFinedFirstParam('objectid');

			$addressObjectCollection = AddressObjectCollection::fromQueryResult(
				$this->db->getChiledNameByObjectIdAndName($objectId, $addressBuilderDirector->getCurrentChiledName())
			);

			if ($addressObjectCollection->isContainsOnlyOneElement()) {
				$addressBuilderDirector->addChiledAddr($addressObjectCollection);
			} else {
				$addressBuilderDirector->addChiledVariant($addressObjectCollection);
			}

		} elseif ($addressObjectCollection->isNotEmpty()) {
			$addressBuilderDirector->addChiledVariant($addressObjectCollection);

		}

	}

    /**
     * @param array<string> $userAddress
     * @return void
     */ 
	protected function handleComplexUserAddress(array $userAddress): void
	{
		try {
			$chain = $this->findSimilarAddressChain($userAddress);
		} catch (RuntimeException) {
			return;
		}

        $addressBuilderDirector = AddressBuilderDirector::fromChainPoint($this->addressBuilder, $userAddress, $chain);

        $this->completeAddressChainBackward($addressBuilderDirector, $chain->parentObjectId);
        $this->completeAddressChainForward($addressBuilderDirector, $chain->chiledObjectId);
    }


	/**
	 * @param array<string> $userAddress
	 * @return ChainPoint
	 * @throws RuntimeException
	 */
	protected function findSimilarAddressChain(array $userAddress): ChainPoint
	{
		$addressObjectCount = count($userAddress);

		for ($parent = 0, $chiled = 1; $chiled < $addressObjectCount; ++$parent, ++$chiled) {
			$chainObjectId = $this->db->findChainByParentAndChiledAddressName($userAddress[$parent], $userAddress[$chiled]);

			// check if chain is single value
			if (count($chainObjectId) === 1) {
				// if it true we unwrap it and return chain element object
                $pointObjectId = array_values($chainObjectId[0]);

				return ChainPoint::fromQueryResult($pointObjectId, $parent, $chiled);
			}
		}

		// if chin was not found we throw a exception
		throw new RuntimeException('chain was not found');
	}

	/**
	 * @param AddressBuilderDirector $addressBuilderDirector
	 * @param int $objectId - address object id
	 */
    protected function completeAddressChainBackward(AddressBuilderDirector $addressBuilderDirector, int $objectId): void
    {
	    $addressObjectCollection = AddressObjectCollection::fromQueryResult(
			$this->db->getSingleNameByObjectId($objectId)
	    );

	    while ($addressObjectCollection->isNotEmpty()) {
			$addressBuilderDirector->addParentAddr($addressObjectCollection);

			$objectId = $addressObjectCollection->tryFinedFirstParam('objectid');

			$addressObjectCollection = AddressObjectCollection::fromQueryResult(
				$this->db->getParentAddressByObjectId($objectId)
			);
		}
    }

	/**
	 * @param AddressBuilderDirector $addressBuilderDirector
	 * @param int $objectId - address object id
     */ 
    protected function completeAddressChainForward(AddressBuilderDirector $addressBuilderDirector, int $objectId): void
    {
	    $addressObjectCollection = AddressObjectCollection::fromQueryResult(
		    $this->db->getSingleNameByObjectId($objectId)
	    );

        while ($addressObjectCollection->isContainsOnlyOneElement())
        {
	        $addressBuilderDirector->addChiledAddr($addressObjectCollection);

			try {
				$chiledName = $addressBuilderDirector->getCurrentChiledName();
			} catch (RuntimeException) {
				break;
			}

	        $objectId = $addressObjectCollection->tryFinedFirstParam('objectid');

			$addressObjectCollection = AddressObjectCollection::fromQueryResult(
				$this->db->getChiledNameByObjectIdAndName($objectId, $chiledName)
			);
        }

		if ($addressObjectCollection->hasMany()) {
			$addressBuilderDirector->addChiledVariant($addressObjectCollection);
		} else {
			$houseCollection = HouseCollection::fromQueryResult(
				$this->db->getHousesByObjectId($objectId)
			);
			if ($houseCollection->isNotEmpty()) {
				$addressBuilderDirector->addChiledHouses($houseCollection);
			}
		}
    }
}