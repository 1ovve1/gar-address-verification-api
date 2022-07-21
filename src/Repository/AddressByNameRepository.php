<?php

declare(strict_types=1);

namespace GAR\Repository;

use GAR\Database\Table\SQL\QueryModel;

const LEVEL = 5;

/**
 * Repo that contains methods that use for get full name of address by specific name address
 */
class AddressByNameRepository extends BaseRepo
{
    /**
     * Return full address by fragment of $halfAddress
     * @param  array<string>  $halfAddress - exploided input address fragment
     * @return array<string, array<mixed>> - trully full address
     */
    public function getFullAddress(array $halfAddress): array
    {
        $fullAddress = [];


        if (count($halfAddress) === 1) {
            $singleName = $halfAddress[0];
            $checkLikeAddress = $this->getLikeAddress($singleName);
            if (!empty($checkLikeAddress)) {
                $fullAddress[][(empty($singleName)) ? 'variants' : $singleName] = $checkLikeAddress;
            }
        } else {
            $objectId = [];

            for ($parent = 0, $chiled = 1; $chiled < count($halfAddress); ++$parent, ++$chiled) {
                $objectId = $this->getAddressObjectIdByName($halfAddress[$parent], $halfAddress[$chiled]);
                if (count($objectId) === 1) {
                    break;
                }
            }

            if (count($objectId) === 1) {
                $objectIdCursor = $this->getObjectIdFromResult($objectId);

                // upper
                $upperChiledObjectId = $objectIdCursor;
                $parentName = '';

                for (; ; --$parent) {
                    $parentCheck = $this->getParentNameByObjectId($upperChiledObjectId);

                    if ($parent >= 0) {
                        $parentName = $halfAddress[$parent];
                    } elseif (count($parentCheck) === 1) {
                        static $id = 1;
                        $parentName = 'parent_' . $id++;
                    }

                    if (!empty($parentCheck)) {
                        if (count($parentCheck) === 1) {
                            $fullAddress[] = [$parentName => $parentCheck];
                        } else {
                            $fullAddress[] = ['parent_variants' => $parentCheck];
                            break;
                        }
                    } else {
                        break;
                    }
                    $upperChiledObjectId = $this->getObjectIdFromResult(end($fullAddress)[$parentName]);
                }
                //reverse
                $fullAddress = array_reverse($fullAddress);

                //middle
                $fullAddress[][$halfAddress[$chiled - 1]] = $this->getSingleNameByObjectId($objectIdCursor);

                // down
                $downChiledObjectId = $objectIdCursor;

                for (; $chiled < count($halfAddress); ++$chiled) {
                    $chiledName = $halfAddress[$chiled];
                    $chiledVariant = $this->getChiledNameByObjectIdAndName($downChiledObjectId, $chiledName);
                    if (count($chiledVariant) === 1 && $chiledName !== '') {
                        $fullAddress[][$chiledName] = $chiledVariant;
                        $downChiledObjectId = $this->getObjectIdFromResult(end($fullAddress)[$chiledName]);
                    } elseif (!empty($chiledVariant)) {
                        $fullAddress[]['variant'] = $chiledVariant;
                        break;
                    }
                }
                if (!array_key_exists('variant', end($fullAddress))) {
                    $housesCheck = $this->getHousesByObjectId($downChiledObjectId);
                    if (!empty($housesCheck)) {
                        $fullAddress[]['houses'] = $housesCheck;
                    }
                }
            }
        }


        return $fullAddress;
    }

    /**
     * Return singlename address name by objectud param of concrete address
     * @param  int    $objectId - object id concrete address
     * @return array<mixed>
     */
    public function getSingleNameByObjectId(int $objectId): array
    {
        $hierarchy = $this->getDatabase();

        if (!$hierarchy->nameExist('getSingleNameByObjectId')) {
            $hierarchy->select(['addr.name', 'addr.typename', 'addr.objectid'], ['addr' => 'addr_obj'])
      ->where('addr.objectid', '=', $objectId)
      ->name('getSingleNameByObjectId');
        }

        return $hierarchy->execute([$objectId], 'getSingleNameByObjectId');
    }

    /**
     * Return chiled name of using parent objectid and chiled name fragment
     * @param  int    $parentObjectId - parent address objectid
     * @param  string $chiledName - chiled name fragment
     * @return array<mixed>
     */
    protected function getChiledNameByObjectIdAndName(int $parentObjectId, string $chiledName): array
    {
        $hierarchy = $this->getDatabase();

        if (!$hierarchy->nameExist('getChiledNameByObjectIdAndName')) {
            $hierarchy->select(['chiled.name', 'chiled.typename', 'chiled.objectid'], ['mun' => 'mun_hierarchy'])
        ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
        ->where('mun.parentobjid_addr', '=', $parentObjectId)
        ->andWhere("CONCAT(chiled.name, ' ', chiled.typename)", 'LIKE', $chiledName . '%')
        ->orWhere('mun.parentobjid_addr', '=', $parentObjectId)
        ->andWhere("CONCAT(chiled.typename, ' ', chiled.name)", 'LIKE', $chiledName . '%')
        ->limit(50)
        ->name('getChiledNameByObjectIdAndName');
        }

        return $hierarchy->execute([
      $parentObjectId, $chiledName . '%',
      $parentObjectId, $chiledName . '%'], 'getChiledNameByObjectIdAndName');
    }

    /**
     * Return parent name using chiled address objectid
     * @param  int    $chiledObjectId - chiled address objectid
     * @return array<mixed>
     */
    protected function getParentNameByObjectId(int $chiledObjectId): array
    {
        $hierarchy = $this->getDatabase();

        if (!$hierarchy->nameExist('getParentNameByObjectId')) {
            $hierarchy->select(['parent.name', 'parent.typename', 'parent.objectid'], ['mun' => 'mun_hierarchy'])
        ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
        ->where('mun.chiledobjid_addr', '=', $chiledObjectId)
        ->name('getParentNameByObjectId');
        }

        return $hierarchy->execute([$chiledObjectId], 'getParentNameByObjectId');
    }

    /**
     * Return houses object id using parent address objectid
     * @param  int    $objectId - parent address objectid
     * @return array<mixed>
     */
    protected function getHousesByObjectId(int $objectId): array
    {
        $hierarchy = $this->getDatabase();

        if (!$hierarchy->nameExist('getHousesByObjectId')) {
            $hierarchy->select([
        "TRIM(' ' FROM CONCAT(
          COALESCE(ht.short, ''), ' ', COALESCE(chiled.housenum, ''), ' ',
          COALESCE(addht1.short, ''), ' ', COALESCE(chiled.addnum1, ''), ' ',
          COALESCE(addht2.short, ''), ' ', COALESCE(chiled.addnum2, '')
        )) as house"
      ], ['mun' => 'mun_hierarchy'])
      ->innerJoin('houses as chiled', ['chiled.objectid' => 'mun.chiledobjid_houses'])
      ->leftJoin('housetype as ht', ['ht.id' => 'chiled.id_housetype'])
      ->leftJoin('addhousetype as addht1', ['addht1.id' => 'chiled.id_addtype1'])
      ->leftJoin('addhousetype as addht2', ['addht2.id' => 'chiled.id_addtype2'])
      ->where('mun.parentobjid_addr', '=', $objectId)
      ->name('getHousesByObjectId');
        }

        return $hierarchy
      ->execute([$objectId], 'getHousesByObjectId');
    }

    /**
     * Return parent address object id by parent and chiled address name
     * @param  string $parentName - parent address name
     * @param  string $chiledName - chiled address name
     * @return array<mixed>
     */
    protected function getAddressObjectIdByName(string $parentName, string $chiledName): array
    {
        $hierarchy = $this->getDatabase();

        if (!$hierarchy->nameExist('getAddressObjectIdByName')) {
            $hierarchy->select(['DISTINCT(parent.objectid)'], ['mun' => 'mun_hierarchy'])
        ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
        ->leftJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
        ->where("CONCAT(parent.name, ' ', parent.typename)", 'LIKE', $parentName . '%')
        ->andWhere("CONCAT(chiled.name, ' ', chiled.typename)", 'LIKE', $chiledName . '%')
        ->andWhere('parent.id_level', '<=', LEVEL)
        ->orWhere("CONCAT(parent.typename, ' ',parent.name)", 'LIKE', $parentName . '%')
        ->andWhere("CONCAT(chiled.name, ' ', chiled.typename)", 'LIKE', $chiledName . '%')
        ->andWhere('parent.id_level', '<=', LEVEL)
        ->orWhere("CONCAT(parent.typename, ' ', parent.name)", 'LIKE', $parentName . '%')
        ->andWhere("CONCAT(chiled.typename, ' ', chiled.name)", 'LIKE', $chiledName . '%')
        ->andWhere('parent.id_level', '<=', LEVEL)
        ->orWhere("CONCAT(parent.name, ' ', parent.typename)", 'LIKE', $parentName . '%')
        ->andWhere("CONCAT(chiled.name, ' ', chiled.typename)", 'LIKE', $chiledName . '%')
        ->andWhere('parent.id_level', '<=', LEVEL)
        ->limit(2)
        ->name('getAddressObjectIdByName');
        }

        return $hierarchy->execute([
      $parentName . '%', $chiledName . '%', LEVEL,
      $parentName . '%', $chiledName . '%', LEVEL,
      $parentName . '%', $chiledName . '%', LEVEL,
      $parentName . '%', $chiledName . '%', LEVEL], 'getAddressObjectIdByName');
    }

    /**
     * Return like address name by address name fragment
     * @param  string $halfAddress - address name fragment
     * @return array<mixed>
     */
    protected function getLikeAddress(string $halfAddress): array
    {
        $hierarchy = $this->getDatabase();

        if (!$hierarchy->nameExist('getLikeAddress')) {
            $hierarchy->select(['addr.name', 'addr.typename', 'addr.objectid'], ['addr' => 'addr_obj'])
        ->where("CONCAT(addr.name, ' ', addr.typename)", 'LIKE', $halfAddress . '%')
        ->andWhere('id_level', '<=', LEVEL)
        ->orWhere("CONCAT(addr.typename, ' ', addr.name)", 'LIKE', $halfAddress . '%')
        ->andWhere('id_level', '<=', LEVEL)
        ->limit(100)
        ->name('getLikeAddress');
        }

        return $hierarchy->execute([
      $halfAddress . '%', LEVEL,
      $halfAddress . '%', LEVEL
      ], 'getLikeAddress');
    }

    /**
     * Return imploded address name + typename string
     * @param  array<string, array<int, array<mixed>>> $address - address array
     * @return string
     */
    protected function getFullAddressByArray(array $address): string
    {
        $formatted = [];
        foreach (array_reverse($address) as $contains) {
            if (count($contains) > 1) {
                continue;
            }
            foreach ($contains as $elem) {
                if (
          array_key_exists('name', $elem) && array_key_exists('typename', $elem)
        ) {
                    $formatted[] = implode(' ', [$elem['name'], $elem['typename']]);
                }
            }
        }

        return implode(', ', $formatted);
    }

    /**
     * Save return 'objectid' field from query result
     * @param  array<mixed>  $queryResult - result of query
     * @return int
     * @throws \RuntimeException
     */
    protected function getObjectIdFromResult(array $queryResult): int
    {
        if (is_array($queryResult[0])) {
            if (key_exists('objectid', $queryResult[0])) {
                $data = $queryResult[0]['objectid'];
                if (is_int($data)) {
                    return $data;
                } else {
                    throw new \RuntimeException("AddressByNameRepository error: objectid are not int");
                }
            } else {
                throw new \RuntimeException("AddressByNameRepository error: field 'objectid' are not exists");
            }
        } else {
            throw new \RuntimeException("AddressByNameRepository error: queryResult is empty");
        }
    }
}
