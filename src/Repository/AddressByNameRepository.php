<?php declare(strict_types=1);

namespace GAR\Repository;

use GAR\Database\Table\SQL\QueryModel;

const LEVEL = 5;

class AddressByNameRepository extends BaseRepo
{

  public function getFullAddress(array $halfAddress) : array
  {
    $fullAddress = [];


    if (count($halfAddress) === 1) {
      $singleName = $halfAddress[0];
      $checkLikeAddress = $this->getLikeAddress($singleName);
      if (!empty($checkLikeAddress)) {
        $fullAddress[$singleName] = $checkLikeAddress;
      }

    } else {
      $objectId = [];

      for ($parent = 0, $chiled = 1; $chiled < count($halfAddress); ++$parent, ++$chiled)  {

        $objectId = $this->getAddressObjectIdByName($halfAddress[$parent], $halfAddress[$chiled]);
        if (count($objectId) === 1) {
          break;
        }
      }

      if (count($objectId) === 1) {
        // upper
        $objectIdCursor = $objectId[0]['objectid'];
        $upperChiledObjectId = $objectIdCursor;
        for(; ; --$parent) {
          if ($parent >= 0) {
            $parentName = $halfAddress[$parent];
          } else {
            static $id = 1;
            $parentName = 'parent_' . $id++;
          }
          $parentCheck = $this->getParentNameByObjectId($upperChiledObjectId);

          if (!empty($parentCheck)) {
            $fullAddress = array_merge([$parentName => $parentCheck], $fullAddress);
          } else {
            break;
          }
          $upperChiledObjectId = $fullAddress[$parentName][0]['objectid'];
        }

        //middle
        $fullAddress[$halfAddress[$chiled - 1]] = $this->getSingleNameByObjectId($objectIdCursor);

        // down
        $downChiledObjectId = $objectIdCursor;
        for(; $chiled < count($halfAddress); ++$chiled) {
          $chiledName = $halfAddress[$chiled];
          $chiledVariant = $this->getChiledNameByObjectIdAndName($downChiledObjectId, $chiledName);
          if (count($chiledVariant) === 1 && $chiledName !== '') {
            $fullAddress[$chiledName] = $chiledVariant;
            $downChiledObjectId = $fullAddress[$chiledName][0]['objectid'];
          } else {
            $fullAddress['variant'] = $chiledVariant;
            break;
          }
        }
        if (!array_key_exists('variant', $fullAddress)) {
          $housesCheck = $this->getHousesByObjectId($downChiledObjectId);
          if (!empty($housesCheck)) {
            $fullAddress['houses'] = $housesCheck;
          }
        }

      }

    }


    return $fullAddress;
  }

  public function getSingleNameByObjectId (int $objectId) : array {
    $hierarchy = $this->getDatabase();

    if (!$hierarchy->nameExist('getSingleNameByObjectId')) {
      $hierarchy->select(['addr.name', 'addr.typename', 'addr.objectid'], ['addr' => 'addr_obj'])
      ->where('addr.objectid', '=', $objectId)
      ->name('getSingleNameByObjectId');
    }

    return $hierarchy->execute([$objectId], 'getSingleNameByObjectId');
  }

  protected function getChiledNameByObjectIdAndName (int $parentObjectId, string $chiledName) : array
  {
    $hierarchy = $this->getDatabase();

    if (!$hierarchy->nameExist('getChiledNameByObjectIdAndName')) {
      $hierarchy->select(['chiled.name', 'chiled.typename', 'chiled.objectid'], ['mun' => 'mun_hierarchy'])
        ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
        ->where('mun.parentobjid_addr', '=', $parentObjectId)
        ->andWhere('chiled.name', 'LIKE', $chiledName . '%')
        ->limit(50)
        ->name('getChiledNameByObjectIdAndName');
    }

    return $hierarchy->execute([$parentObjectId, $chiledName . '%'], 'getChiledNameByObjectIdAndName');
  }

  protected function getParentNameByObjectId (int $chiledObjectId) : array
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

  protected function getHousesByObjectId(int $objectId) : array
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

  protected function getAddressObjectIdByName(string $parentName, string $chiledName) : array
  {
    $hierarchy = $this->getDatabase();

    if (!$hierarchy->nameExist('getAddressObjectIdByName')) {
      $hierarchy->select(['DISTINCT(parent.objectid)'], ['mun' => 'mun_hierarchy'])
        ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
        ->leftJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
        ->where('parent.name', 'LIKE', $parentName . '%')
        ->andWhere('chiled.name', 'LIKE', $chiledName . '%')
        ->andWhere('parent.id_level', '<=', LEVEL)
        ->limit(2)
        ->name('getAddressObjectIdByName');
    }

    return $hierarchy->execute([$parentName . '%', $chiledName . '%', LEVEL], 'getAddressObjectIdByName');
  }

  protected function getLikeAddress(string $halfAddress) : array
  {
    $hierarchy = $this->getDatabase();

    if (!$hierarchy->nameExist('getLikeAddress')) {
      $hierarchy->select(['addr.name', 'addr.typename', 'addr.objectid'], ['addr' => 'addr_obj'])
      ->where('addr.name', 'LIKE', $halfAddress . '%')
      ->andWhere('id_level', '<=', LEVEL)
      ->limit(100)
      ->name('getLikeAddress');
    }

    return $hierarchy->execute([$halfAddress . '%', LEVEL], 'getLikeAddress');
  }

  protected function getFullAddressByArray(array $address) : string
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


}