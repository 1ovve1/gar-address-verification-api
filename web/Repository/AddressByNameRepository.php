<?php

declare(strict_types=1);

namespace GAR\Repository;

use GAR\Repository\Address\AddressBuilder;
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
//            $objectid = [];
//
//            for ($parent = 0, $chiled = 1; $chiled < count($userAddress); ++$parent, ++$chiled) {
//                $objectid = $db->getAddressObjectIdByName($userAddress[$parent], $userAddress[$chiled]);
//                if (count($objectid) === 1) {
//                    break;
//                }
//            }
//
//            if (count($objectid) === 1) {
//                $pointObjectId = $this->getObjectIdAndRegionFromResult($objectid);
//
//                // upper
//                $upperChiledObjectId = $pointObjectId;
//                $parentName = '';
//
//                for (; ; --$parent) {
//                    $parentCheck = $db->getParentNameByObjectId($upperChiledObjectId);
//
//                    if ($parent >= 0) {
//                        $parentName = $userAddress[$parent];
//                    } elseif (count($parentCheck) === 1) {
//                        static $id = 1;
//                        $parentName = 'parent_' . $id++;
//                    }
//
//                    if (!empty($parentCheck)) {
//                        if (count($parentCheck) === 1) {
//                            $fullAddress[] = [
//                                $parentName => $parentCheck,
//                            ];
//                        } else {
//                            $fullAddress[] = [
//                                'parent_variants' => $parentCheck,
//                            ];
//                            break;
//                        }
//                    } else {
//                        break;
//                    }
//                    $upperChiledObjectId = $this->getObjectIdAndRegionFromResult(end($fullAddress)[$parentName]);
//                }
//                //reverse
//                $fullAddress = array_reverse($fullAddress);
//
//                //middle
//                $fullAddress[][$userAddress[$chiled - 1]] = $db->getSingleNameByObjectId($pointObjectId);
//
//                // down
//                $downChiledObjectId = $pointObjectId;
//
//                for (; $chiled < count($userAddress); ++$chiled) {
//                    $chiledName = $userAddress[$chiled];
//                    $chiledVariant = $db->getChiledNameByObjectIdAndName($downChiledObjectId, $chiledName);
//                    if (count($chiledVariant) === 1 && $chiledName !== '') {
//                        $fullAddress[][$chiledName] = $chiledVariant;
//                        $downChiledObjectId = $this->getObjectIdAndRegionFromResult(end($fullAddress)[$chiledName]);
//                    } elseif (!empty($chiledVariant)) {
//                        $fullAddress[]['variant'] = $chiledVariant;
//                        break;
//                    }
//                }
//                if (!array_key_exists('variant', end($fullAddress))) {
//                    $housesCheck = $db->getHousesByObjectId($downChiledObjectId);
//                    if (!empty($housesCheck)) {
//                        $fullAddress[]['houses'] = $housesCheck;
//                    }
//                }
//            }



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

	protected function handleComplexAddress(array $userAddress): void
	{
		[$parentObjectId, $chiledObjectId] = $this->findSimilarAddressChain($userAddress) ?? [null, null];

		if (null !== $parentObjectId && null !== $chiledObjectId) {
			$pointObjectId = $this->getObjectIdAndRegionFromResult($objectid);

			// upper
			$upperChiledObjectId = $pointObjectId;
			$parentName = '';

			for (; ; --$parent) {
				$parentCheck = $db->getParentNameByObjectId($upperChiledObjectId);

				if ($parent >= 0) {
					$parentName = $userAddress[$parent];
				} elseif (count($parentCheck) === 1) {
					static $id = 1;
					$parentName = 'parent_' . $id++;
				}

				if (!empty($parentCheck)) {
					if (count($parentCheck) === 1) {
						$fullAddress[] = [
							$parentName => $parentCheck,
						];
					} else {
						$fullAddress[] = [
							'parent_variants' => $parentCheck,
						];
						break;
					}
				} else {
					break;
				}
				$upperChiledObjectId = $this->getObjectIdAndRegionFromResult(end($fullAddress)[$parentName]);
			}
			//reverse
			$fullAddress = array_reverse($fullAddress);

			//middle
			$fullAddress[][$userAddress[$chiled - 1]] = $db->getSingleNameByObjectId($pointObjectId);

			// down
			$downChiledObjectId = $pointObjectId;

			for (; $chiled < count($userAddress); ++$chiled) {
				$chiledName = $userAddress[$chiled];
				$chiledVariant = $db->getChiledNameByObjectIdAndName($downChiledObjectId, $chiledName);
				if (count($chiledVariant) === 1 && $chiledName !== '') {
					$fullAddress[][$chiledName] = $chiledVariant;
					$downChiledObjectId = $this->getObjectIdAndRegionFromResult(end($fullAddress)[$chiledName]);
				} elseif (!empty($chiledVariant)) {
					$fullAddress[]['variant'] = $chiledVariant;
					break;
				}
			}
			if (!array_key_exists('variant', end($fullAddress))) {
				$housesCheck = $db->getHousesByObjectId($downChiledObjectId);
				if (!empty($housesCheck)) {
					$fullAddress[]['houses'] = $housesCheck;
				}
			}
		}
	}

	/**
	 * @param array<string> $userAddress
	 * @return array{int, int}|null
	 */
	protected function findSimilarAddressChain(array $userAddress): array|null
	{
		$addressObjectCount = count($userAddress);

		for ($parent = 0, $chiled = 1; $chiled < $addressObjectCount; ++$parent, ++$chiled) {
			$chainObjectId = $this->db->findChainByParentAndChiledAddressName($userAddress[$parent], $userAddress[$chiled]);

			// check if chain is single value
			if (count($chainObjectId) === 1) {
				// if it true we unwrap it and return
				return array_values($chainObjectId[0]);
			}
		}

		// if chin was not found we return null
		return null;
	}

    /**
     * Save return 'objectid' field from query result
     * @param  array<mixed>  $queryResult - result of query
     * @return int
     * @throws RuntimeException
     */
    protected function getObjectIdAndRegionFromResult(array $queryResult): int
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
