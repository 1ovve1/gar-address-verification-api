<?php

declare(strict_types=1);

namespace GAR\Repository;

use GAR\Repository\Address\AddressBuilder;
use GAR\Repository\Address\AddressBuilderDirector;
use GAR\Repository\Address\AddressBuilderImplement;
use RuntimeException;


/**
 * Repo that contains methods that use for get full name of address by specific name address
 */
class AddressByNameRepository extends BaseRepo
{
	protected AddressBuilder $addressBuilder;

	/**
	 * @inheritDoc
	 */
	public function __construct()
	{
		$this->addressBuilder = new AddressBuilderImplement();
		parent::__construct();
	}

	protected function initAddressBuilder(): void
	{
		$this->addressBuilder = new AddressBuilderImplement();
	}

	/**
     * Return full address by fragment of $halfAddress
     * @param  array<string>  $userAddress - exploded input address fragment
     * @return array<int, array<string, array<string, mixed>>> - full address
     */
    public function getFullAddress(array $userAddress): array
    {
		$this->initAddressBuilder();

		if ($this->isUserAddressAreSingleWord($userAddress)) {
			$this->findSimilarAddressObjectFromDb(current($userAddress));

		} else {
			$this->handleComplexAddress($userAddress);

		}

        return $this->addressBuilder->getAddress();
    }

	/**
	 * @param array<string> $userAddress
	 * @return bool
	 */
	protected function isUserAddressAreSingleWord(array $userAddress): bool
	{
		return count($userAddress) === 1;
	}

	/**
	 * @param string $addressName
	 * @return void
	 */
	protected function findSimilarAddressObjectFromDb(string $addressName): void
	{
		$checkLikeAddress = $this->db->getLikeAddress($addressName);

		if (!empty($checkLikeAddress)) {
			$this->addressBuilder->addChiledVariant($checkLikeAddress);
		}
	}

    /**
     * @param array<string> $userAddress
     * @return void
     */ 
	protected function handleComplexAddress(array $userAddress): void
	{
        $pointStructure = $this->findSimilarAddressChain($userAddress);

        if (null !== $pointStructure) {
        	$addressBuilderDirector = new AddressBuilderDirector($this->addressBuilder, $userAddress, $pointStructure['index']['parent'], $pointStructure['index']['chiled']);

            $this->completeAddressChainBackward($addressBuilderDirector, $pointStructure['objectId']['parent']);
            $this->completeAddressChainForward($addressBuilderDirector, $pointStructure['objectId']['chiled']);
        }
    }
		

	/**
	 * @param array<string> $userAddress
	 * @return array{objectId: array{int, int}, point: array{int, int}}|null
	 */
	protected function findSimilarAddressChain(array $userAddress): array|null
	{
		$addressObjectCount = count($userAddress);

		for ($parent = 0, $chiled = 1; $chiled < $addressObjectCount; ++$parent, ++$chiled) {
			$chainObjectId = $this->db->findChainByParentAndChiledAddressName($userAddress[$parent], $userAddress[$chiled]);

			// check if chain is single value
			if (count($chainObjectId) === 1) {
				// if it true we unwrap it and return
                $pointObjectId = array_values($chainObjectId[0]);

				return ['objectId' => 
                            ['parent' => $pointObjectId[0], 
                             'chiled' => $pointObjectId[1]], 
                        'index' => 
                            ['parent' => $parent, 
                             'chiled' => $chiled]];
			}
		}

		// if chin was not found we return null
		return null;
	}

    /**
     * @param AddressBuilderDirecotor
     * @param int $objectId - address object id
     */ 
    protected function completeAddressChainBackward(AddressBuilderDirector $addressBuilderDirector, int $objectId): void
    {
    	$current = $this->db->getSingleNameByObjectId($objectId);
    	$addressBuilderDirector->addParentAddr($current);

        while ($parent = $this->db->getParentAddressByObjectId($objectId))
        {
            $addressBuilderDirector->addParentAddr($parent);
        }
    }

	/**
	 * @param AddressBuilderDirector
     * @param int $objectId - address object id
     */ 
    protected function completeAddressChainForward(AddressBuilderDirector $addressBuilderDirector, int $objectId): void
    {
    	$current = $this->db->getSingleNameByObjectId($objectId);
    	$addressBuilderDirector->addChiledAddr($current);

        while ($addressBuilderDirector->isChiledPosNotOverflow() && $chiled = $this->db->getChiledNameByObjectIdAndName($objectId, $addressBuilderDirector->getCurrentChiledName()))
        {
        	if (count($chiled) === 1) {
            	$addressBuilderDirector->addChiledAddr($chiled);
        		$objectId = $this->getObjectIdFromResult($chiled);

        	} else {
        		$addressBuilderDirector->addChiledVariant($chiled);
        		break;
        	}
        }

        if (!$addressBuilderDirector->isChainEndsByVariant()) {
        	$houses = $this->db->getHousesByObjectId($objectId);
        	$addressBuilderDirector->addChiledHouses($houses);
        }
    }

    /**
     * Save return 'objectid' field from query result
     * @param  array<mixed>  $queryResult - result of query
     * @return int
     * @throws RuntimeException
     */
    protected function getObjectIdFromResult(array $queryResult): int
    {
        if (is_array($queryResult[0])) {
            if (key_exists('objectid', $queryResult[0])) {
                $objectid = $queryResult[0]['objectid'];
                if (is_int($objectid)) {
                     return $queryResult[0]['objectid'];
                } else {
                    throw new RuntimeException("AddressByNameRepository error: objectid are not int");
                }
            } else {
                throw new RuntimeException("AddressByNameRepository error: field 'objectid' are not exists");
            }

        } else {
            throw new RuntimeException("AddressByNameRepository error: queryResult is empty");
        }
    }
}
