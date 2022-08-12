<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\Models\Database;
use RuntimeException;

const LEVEL = 5;

/**
 * Repo that contains methods that use for get full name of address by specific name address
 */
class AddressByNameRepository extends BaseRepo
{
    /**
     * Return full address by fragment of $halfAddress
     * @param  array<string>  $halfAddress - exploided input address fragment
     * @return array<int, array<string, array<string, mixed>>> - trully full address
     */
    public function getFullAddress(array $halfAddress): array
    {
        $fullAddress = [];
		$table = $this->getDatabase();

        if (count($halfAddress) === 1) {
            $singleName = $halfAddress[0];
            $checkLikeAddress = $table->getLikeAddress($singleName);
            if (!empty($checkLikeAddress)) {
                $fullAddress[][(empty($singleName)) ? 'variants' : $singleName] = $checkLikeAddress;
            }
        } else {
            $objectid = [];

            for ($parent = 0, $chiled = 1; $chiled < count($halfAddress); ++$parent, ++$chiled) {
                $objectid = $table->getAddressObjectIdByName($halfAddress[$parent], $halfAddress[$chiled]);
                if (count($objectid) === 1) {
                    break;
                }
            }

            if (count($objectid) === 1) {
                $pointObjectId = $this->getObjectIdAndRegionFromResult($objectid);

                // upper
                $upperChiledObjectId = $pointObjectId;
                $parentName = '';

                for (; ; --$parent) {
                    $parentCheck = $table->getParentNameByObjectId($upperChiledObjectId);

                    if ($parent >= 0) {
                        $parentName = $halfAddress[$parent];
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
                $fullAddress[][$halfAddress[$chiled - 1]] = $table->getSingleNameByObjectId($pointObjectId);

                // down
                $downChiledObjectId = $pointObjectId;

                for (; $chiled < count($halfAddress); ++$chiled) {
                    $chiledName = $halfAddress[$chiled];
                    $chiledVariant = $table->getChiledNameByObjectIdAndName($downChiledObjectId, $chiledName);
                    if (count($chiledVariant) === 1 && $chiledName !== '') {
                        $fullAddress[][$chiledName] = $chiledVariant;
                        $downChiledObjectId = $this->getObjectIdAndRegionFromResult(end($fullAddress)[$chiledName]);
                    } elseif (!empty($chiledVariant)) {
                        $fullAddress[]['variant'] = $chiledVariant;
                        break;
                    }
                }
                if (!array_key_exists('variant', end($fullAddress))) {
                    $housesCheck = $table->getHousesByObjectId($downChiledObjectId);
                    if (!empty($housesCheck)) {
                        $fullAddress[]['houses'] = $housesCheck;
                    }
                }
            }
        }


        return $fullAddress;
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
