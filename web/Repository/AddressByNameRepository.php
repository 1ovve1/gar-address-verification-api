<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\Exceptions\BadQueryResultException;
use DB\Exceptions\FailedDBConnectionWithDBException;
use GAR\Exceptions\AddressNotFoundException;
use GAR\Exceptions\ChainNotFoundException;
use GAR\Exceptions\ParamNotFoundException;
use GAR\Exceptions\ServerSideProblemException;
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
	 * @param array<string> $userAddress
	 * @return int
	 * @throws AddressNotFoundException
	 * @throws ParamNotFoundException - if objectid was not found
	 * @throws ServerSideProblemException
	 * @throws FailedDBConnectionWithDBException
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
	 * @throws AddressNotFoundException - address was not found
	 * @throws FailedDBConnectionWithDBException
	 * @throws ServerSideProblemException - bd server error
	 */
    function getFullAddress(array $userAddress): array
    {
		$this->addressBuilderDirector = new AddressBuilderDirector($this->addressBuilder, $userAddress);

		try {
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
		} catch (BadQueryResultException $e) {
			throw new ServerSideProblemException($e);
		}

		$address = $this->addressBuilder->getAddress();

		if (empty($address)) {
			throw new AddressNotFoundException();
		}

        return $address;
    }

	/**
	 * @return void
	 * @throws BadQueryResultException
	 * @throws FailedDBConnectionWithDBException
	 */
	protected function handleSingleWordUserAddress(): void
	{
		$word = $this->addressBuilderDirector->getCurrentChiledName();

		$this->completeAddressChainBackward($word);
	}

	/**
	 * @return void
	 * @throws BadQueryResultException
	 * @throws FailedDBConnectionWithDBException
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

		$chiledLikeAddress = $this->db->getChiledAddressByParentObjectIdAndChiledName($parentObjectId, $currChiledName);

		if ($chiledLikeAddress->hasOnlyOneRow()) {
			$this->addressBuilderDirector->addChiledAddr($chiledLikeAddress);
		} elseif ($chiledLikeAddress->isNotEmpty()) {
			$this->addressBuilderDirector->addChiledVariant($chiledLikeAddress);
		}
	}

	/**
	 * @return void
	 * @throws BadQueryResultException
	 * @throws FailedDBConnectionWithDBException
	 * @throws FailedDBConnectionWithDBException
	 * @throws FailedDBConnectionWithDBException
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
	 * @throws FailedDBConnectionWithDBException
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
	 * @param int|string $currObjectIdOrName
	 * @throws BadQueryResultException
	 * @throws FailedDBConnectionWithDBException
	 */
    protected function completeAddressChainBackward(int|string $currObjectIdOrName): void
    {
	    $parentAddress = match(is_string($currObjectIdOrName)) {
			true => $this->db->getLikeAddress($currObjectIdOrName),
			false => $this->db->getAddressByObjectId($currObjectIdOrName),
        };

	    while ($parentAddress->hasOnlyOneRow()) {
			$this->addressBuilderDirector->addParentAddr($parentAddress);

		    try {
			    $parentObjectId = $this->addressBuilderDirector->findParentObjectId();
		    } catch (ParamNotFoundException) {
				break;
		    }

		    $parentAddress = $this->db->getParentAddressByObjectId($parentObjectId);
		}

		if ($parentAddress->hasManyRows()) {
			$this->addressBuilderDirector->addChiledVariant($parentAddress);
		}
    }

	/**
	 * @param int|string $currObjectIdOrName
	 * @throws BadQueryResultException
	 * @throws FailedDBConnectionWithDBException
	 */
    protected function completeAddressChainForward(int|string $currObjectIdOrName): void
    {
		$chiledAddress = match(is_string($currObjectIdOrName)) {
			true => $this->db->getLikeAddress($currObjectIdOrName),
			false => $this->db->getAddressByObjectId($currObjectIdOrName)
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