<?php

declare(strict_types=1);

namespace GAR\Storage;

use GAR\Exceptions\Checked\AddressNotFoundException;
use GAR\Exceptions\Checked\ChainNotFoundException;
use GAR\Exceptions\Checked\ParamNotFoundException;
use GAR\Exceptions\Unchecked\ServerSideProblemException;
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
			return $this->addressBuilderDirector->getChiledObjectIdFromEnd();
		} catch (ParamNotFoundException) {
			try {
				return $this->addressBuilderDirector->getParentObjectIdFromStart();
			} catch (ParamNotFoundException) {
				throw new AddressNotFoundException();
			}
		}
	}

	/**
	 * Return full address by fragment of $halfAddress
	 * @param array<string> $userAddress - exploded input address fragment
	 * @param int $region - region context
	 * @return AddressJSON - full address
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

		$address = $this->addressBuilderDirector->getAddress();

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
		[$word] = $this->addressBuilderDirector->userAddress;

		$this->completeAddressChainBackward($word);
	}

	/**
	 * @return void
	 */
	function handleDoubleWordUserAddress(): void
	{
		[$parentName, $chiledName] = $this->addressBuilderDirector->userAddress;

		$this->completeAddressChainBackward($parentName);

		if ($this->addressBuilderDirector->isFinal()) {
			return;
		}

		try {
			$parentObjectId = $this->addressBuilderDirector->getParentObjectIdFromStart();
			$chiledLikeAddress = $this->db->getChiledAddressByParentObjectIdAndChiledAddressName($parentObjectId, $chiledName, $this->getRegionContext());

			if ($chiledLikeAddress->hasOnlyOneRow()) {
				$this->addressBuilderDirector->addChiledAddress($chiledLikeAddress);
			} elseif ($chiledLikeAddress->isNotEmpty()) {
				$this->addressBuilderDirector->addVariant($chiledLikeAddress);
			}
		} catch (ParamNotFoundException $ex) {
			return;
		}

	}

	/**
	 * @return void
	 */
	protected function handleComplexUserAddress(): void
	{
		try {
			$chain = $this->findSimilarAddressChain();
		} catch (ChainNotFoundException) {
			return;
		}

		$this->addressBuilderDirector = AddressBuilderDirector::fromChainPoint($this->addressBuilder, $this->addressBuilderDirector->userAddress, $chain);

        $this->completeAddressChainBackward($chain->parentObjectId);
        $this->completeAddressChainForward($chain->chiledObjectId);
    }


	/**
	 * @return ChainPoint
	 * @throws ChainNotFoundException
	 */
	protected function findSimilarAddressChain(): ChainPoint
	{
		$userAddress = $this->addressBuilderDirector->userAddress;
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

		    try {
			    $this->addressBuilderDirector->addParentAddress($parentAddress);
		    } catch (ParamNotFoundException) {
			    $this->addressBuilderDirector->addUnknownParentAddress($parentAddress);
		    }

		    try {
			    $parentObjectId = $this->addressBuilderDirector->getParentObjectIdFromEnd();
		    } catch (ParamNotFoundException $ex) {
			    break;
		    }

		    $parentAddress = $this->db->getParentAddressByChiledObjectId($parentObjectId, $this->getRegionContext());
		}

		if ($parentAddress->hasManyRows()) {
			$this->addressBuilderDirector->addVariant($parentAddress);
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
	        try {
		        $this->addressBuilderDirector->addChiledAddress($chiledAddress);
	        } catch (ParamNotFoundException $e) {
				throw new ServerSideProblemException($e);
	        }

	        try {
				// look forward and try fined next chiled address name
				$nextChiledName = $this->addressBuilderDirector->getCurrentRawChiledName();
				// because we move forward actual chiled address use like a parent
				$parentObjectId = $this->addressBuilderDirector->getChiledObjectIdFromEnd();
			} catch (ParamNotFoundException) {
				break;
			}

	        $chiledAddress = $this->db->getChiledAddressByParentObjectIdAndChiledAddressName($parentObjectId, $nextChiledName, $this->getRegionContext());
        }

		if ($chiledAddress->hasManyRows()) {
			$this->addressBuilderDirector->addVariant($chiledAddress);
		} elseif(isset($parentObjectId)) {
			try {
				$houseName = $this->addressBuilderDirector->getCurrentRawChiledName();
				$houseAddress = $this->db->getHousesByParentObjectId($parentObjectId, $this->getRegionContext(), $houseName);
			} catch (ParamNotFoundException) {
				$houseAddress = $this->db->getHousesByParentObjectId($parentObjectId, $this->getRegionContext());
			}

			if ($houseAddress->isNotEmpty()) {
				$this->addressBuilderDirector->addHouses($houseAddress);
			}
		}
    }


}