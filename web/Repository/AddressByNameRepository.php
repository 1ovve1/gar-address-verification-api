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
	protected AddressBuilderDirector $addressBuilderDirector;

	public function __construct(AddressBuilder $addressBuilder)
	{
		$this->addressBuilder = $addressBuilder;
		parent::__construct();
	}

	/**
	 * @param array $userAddress
	 * @return int
	 * @throws BadQueryResultException
	 * @throws ParamNotFoundException - if objectid was not found
	 */
	function getChiledObjectIdFromAddress(array $userAddress): int
	{
		$this->getFullAddress($userAddress);

		return $this->addressBuilderDirector->findChiledObjectId();
	}

	/**
	 * Return full address by fragment of $halfAddress
	 * @param array<string> $userAddress - exploded input address fragment
	 * @return array<int, array<string, array<int, array<string, string|int>>>> - full address
	 * @throws BadQueryResultException - objectid was not found
	 * @throws ParamNotFoundException - objectid was not found
	 */
    function getFullAddress(array $userAddress): array
    {
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

        return $this->addressBuilder->getAddress();
    }

	/**
	 * @return void
	 * @throws BadQueryResultException
	 */
	protected function handleSingleWordUserAddress(): void
	{
		$word = $this->addressBuilderDirector->getCurrentChiledName();
		$checkLikeAddress = $this->db->getLikeAddress($word);

		if ($checkLikeAddress->hasOnlyOneRow()) {
			$this->addressBuilderDirector->addParentAddr($checkLikeAddress);
		} elseif ($checkLikeAddress->isNotEmpty()) {
			$this->addressBuilderDirector->addChiledVariant($checkLikeAddress);
		}
	}

	/**
	 * @return void
	 * @throws BadQueryResultException
	 * @throws ParamNotFoundException - if objectid was not found
	 */
	function handleDoubleWordUserAddress(): void
	{
		$parentName = $this->addressBuilderDirector->getCurrentParentName();

		$parentLikeAddress = $this->db->getLikeAddress($parentName);

		if ($parentLikeAddress->hasOnlyOneRow()) {
			$this->addressBuilderDirector->addParentAddr($parentLikeAddress);

			$chiledName = $this->addressBuilderDirector->getCurrentParentName();
			$parentObjectId = $this->addressBuilderDirector->findParentObjectId();

			$chiledLikeAddress = $this->db->getChiledAddressByParentObjectIdAndChiledName($parentObjectId, $chiledName);

			if ($chiledLikeAddress->hasOnlyOneRow()) {
				$this->addressBuilderDirector->addChiledAddr($chiledLikeAddress);
			} else {
				$this->addressBuilderDirector->addChiledVariant($chiledLikeAddress);
			}

		} elseif ($parentLikeAddress->isNotEmpty()) {
			$this->addressBuilderDirector->addChiledVariant($parentLikeAddress);

		}
	}

	/**
	 * @return void
	 * @throws BadQueryResultException
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
	 * @param int $currObjectId
	 * @throws BadQueryResultException
	 */
    protected function completeAddressChainBackward(int $currObjectId): void
    {
	    $parentAddress = $this->db->getAddressByObjectId($currObjectId);

	    while ($parentAddress->isNotEmpty()) {
			$this->addressBuilderDirector->addParentAddr($parentAddress);

		    try {
			    $parentObjectId = $this->addressBuilderDirector->findParentObjectId();
		    } catch (ParamNotFoundException) {
				break;
		    }

		    $parentAddress = $this->db->getParentAddressByObjectId($parentObjectId);
		}
    }

	/**
	 * @param int $currObjectId
	 * @throws BadQueryResultException
	 */
    protected function completeAddressChainForward(int $currObjectId): void
    {
	    $chiledAddress = $this->db->getAddressByObjectId($currObjectId);

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

	        $chiledAddress = $this->db->getChiledAddressByParentObjectIdAndChiledName($parentObjectId, $nextChiledName);
        }

		if ($chiledAddress->hasManyRows()) {
			$this->addressBuilderDirector->addChiledVariant($chiledAddress);
		} elseif(isset($parentObjectId)) {
			$houseAddress = $this->db->getHousesByParentObjectId($parentObjectId);

			if ($houseAddress->isNotEmpty()) {
				$this->addressBuilderDirector->addChiledHouses($houseAddress);
			}
		}
    }
}