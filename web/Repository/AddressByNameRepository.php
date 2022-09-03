<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\Exceptions\BadQueryResultException;
use GAR\Exceptions\ChainNotFoundException;
use GAR\Exceptions\ParamNotFoundException;
use GAR\Repository\Builders\AddressBuilder;
use GAR\Repository\Builders\AddressBuilderDirector;
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
	 * @throws BadQueryResultException
	 */
	protected function handleSingleWordUserAddress(string $addressName): void
	{
		$checkLikeAddress = $this->db->getLikeAddress($addressName);

		if ($checkLikeAddress->hasOnlyOneRow()) {
			$this->addressBuilder->addParentAddr($addressName, $checkLikeAddress->fetchAllAssoc());
		} elseif ($checkLikeAddress->isNotEmpty()) {
			$this->addressBuilder->addChiledVariant($checkLikeAddress->fetchAllAssoc());
		}
	}

	/**
	 * @param array<string> $userAddress
	 * @return void
	 * @throws BadQueryResultException
	 * @throws ParamNotFoundException
	 */
	function handleDoubleWordUserAddress(array $userAddress): void
	{
		$addressBuilderDirector = new AddressBuilderDirector($this->addressBuilder, $userAddress);

		$parentName = $addressBuilderDirector->getCurrentParentName();

		$parentLikeAddress = $this->db->getLikeAddress($parentName);

		if ($parentLikeAddress->hasOnlyOneRow()) {
			$addressBuilderDirector->addParentAddr($parentLikeAddress);

			$chiledName = $addressBuilderDirector->getCurrentParentName();
			$parentObjectId = $addressBuilderDirector->findParentObjectId();

			$chiledLikeAddress = $this->db->getChiledAddressByParentObjectIdAndChiledName($parentObjectId, $chiledName);

			if ($chiledLikeAddress->hasOnlyOneRow()) {
				$addressBuilderDirector->addChiledAddr($chiledLikeAddress);
			} else {
				$addressBuilderDirector->addChiledVariant($chiledLikeAddress);
			}

		} elseif ($parentLikeAddress->isNotEmpty()) {
			$addressBuilderDirector->addChiledVariant($parentLikeAddress);

		}
	}

	/**
	 * @param array<string> $userAddress
	 * @return void
	 * @throws BadQueryResultException
	 */
	protected function handleComplexUserAddress(array $userAddress): void
	{
		try {
			$chain = $this->findSimilarAddressChain($userAddress);
		} catch (ChainNotFoundException) {
			return;
		}

		$addressBuilderDirector = AddressBuilderDirector::fromChainPoint($this->addressBuilder, $userAddress, $chain);

        $this->completeAddressChainBackward($addressBuilderDirector, $chain->parentObjectId);
        $this->completeAddressChainForward($addressBuilderDirector, $chain->chiledObjectId);
    }


	/**
	 * @param array<string> $userAddress
	 * @return ChainPoint
	 * @throws BadQueryResultException
	 * @throws ChainNotFoundException
	 */
	protected function findSimilarAddressChain(array $userAddress): ChainPoint
	{
		$userAddressLength = count($userAddress);

		for ($parent = 0, $chiled = 1; $chiled < $userAddressLength; ++$parent, ++$chiled) {
			$parentNameCurrChain = $userAddress[$parent];
			$chiledNameCurrChain = $userAddress[$chiled];

			$chainObjectId = $this->db->findChainByParentAndChiledAddressName($parentNameCurrChain, $chiledNameCurrChain);

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
	 * @param AddressBuilderDirector $addressBuilderDirector
	 * @param int $currObjectId
	 * @throws BadQueryResultException
	 */
    protected function completeAddressChainBackward(AddressBuilderDirector $addressBuilderDirector, int $currObjectId): void
    {
	    $parentAddress = $this->db->getAddressByObjectId($currObjectId);

	    while ($parentAddress->isNotEmpty()) {
			$addressBuilderDirector->addParentAddr($parentAddress);

		    try {
			    $parentObjectId = $addressBuilderDirector->findParentObjectId();
		    } catch (ParamNotFoundException) {
				break;
		    }

		    $parentAddress = $this->db->getParentAddressByObjectId($parentObjectId);
		}
    }

	/**
	 * @param AddressBuilderDirector $addressBuilderDirector
	 * @param int $currObjectId
	 * @throws BadQueryResultException
	 */
    protected function completeAddressChainForward(AddressBuilderDirector $addressBuilderDirector, int $currObjectId): void
    {
	    $chiledAddress = $this->db->getAddressByObjectId($currObjectId);

        while ($chiledAddress->hasOnlyOneRow())
        {
	        $prevChiledName = $addressBuilderDirector->getCurrentChiledName();
	        $addressBuilderDirector->addChiledAddr($chiledAddress);

			try {
				// look forward and try fined next chiled address name
				$nextChiledName = $addressBuilderDirector->getCurrentChiledName();
				// because we move forward actual chiled address use like a parent
				$parentObjectId = $addressBuilderDirector->findObjectIdFromIdentifier($prevChiledName);
			} catch (RuntimeException|ParamNotFoundException) {

				break;
			}

	        $chiledAddress = $this->db->getChiledAddressByParentObjectIdAndChiledName($parentObjectId, $nextChiledName);
        }

		if ($chiledAddress->hasManyRows()) {
			$addressBuilderDirector->addChiledVariant($chiledAddress);
		} elseif(isset($parentObjectId)) {
			$houseAddress = $this->db->getHousesByParentObjectId($parentObjectId);

			if ($houseAddress->isNotEmpty()) {
				$addressBuilderDirector->addChiledHouses($houseAddress);
			}
		}
    }
}