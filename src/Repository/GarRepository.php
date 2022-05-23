<?php declare(strict_types=1);

namespace GAR\Repository;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\EntityFactory;
use GAR\Util\XMLReader\XMLReaderFactory;

class GarRepository
{
  private array $tables;

  public function __construct(EntityFactory $tableFactory)
  {
    $this->tables = [
//      'levelObj' => $tableFactory::getObjectLevels(),
//      'housetype' => $tableFactory::getHousetype(),
//      'addhousetype' => $tableFactory::getAddhousetype(),
      'addrObj' => $tableFactory::getAddressObjectTable(),
//      'addrObjParams' => $tableFactory::getAddressObjectParamsTable(),
//      'houses' => $tableFactory::getHousesTable(),
//      'admin' => $tableFactory::getAdminTable(),
      'mun' => $tableFactory::getMunTable(),
    ];
  }

  public function getFullAddress(array $halfAddress,
                                 bool $withParent = false,
                                 bool $onlyFullAddress = true) : array
  {
    $chiled = array_pop($halfAddress);
    $fullAddress = [];

    if (!empty($halfAddress)) {
      foreach (array_reverse($halfAddress) as $parent) {
        if (empty($fullAddress)) {
          $chiledCheck = $this->getTrueChiled($parent, $chiled);
          if (empty($chiledCheck)) {
            $chiled = $parent;
            continue;
          }
          $fullAddress[(!empty($chiled)) ? $chiled: 'variants'] = $this->getTrueChiled($parent, $chiled);

          $fullAddress[$parent] = $this->getTrueParent($parent, end($fullAddress)[0]['name']);

          if (!array_key_exists('variants', $fullAddress)) {
            $houseCheck = $this->getHouses($fullAddress[$parent][0]['name'], $fullAddress[$chiled][0]['name']);
            if (!empty($houseCheck)) {
              $tmp = [
                'houses' => $houseCheck,
                $chiled => $fullAddress[$chiled],
                $parent => $fullAddress[$parent],
              ];
              $fullAddress = $tmp;
            }
          }
        } else {
          $fullAddress[$parent] = $this->getTrueParent($parent, $chiled);
        }
        $chiled = end($fullAddress)[0]['name'];
      }
    } else {
      $chiledCheck = $this->getLikeAddress($chiled, $onlyFullAddress);
      if (!empty($chiledCheck)) {
        $fullAddress[$chiled] = $chiledCheck;
      }
    }

    if ($withParent) {
      $parentId = 1;
      while(count(end($fullAddress)) === 1) {
        $chiled = end($fullAddress)[0]['name'];
        $parent = $this->getLikeParent($chiled);
        if (empty($parent)) {
          break;
        } else {
          $fullAddress['parent_'.$parentId++] = $this->getLikeParent($chiled);
        }
      }
    }

    if (!empty($fullAddress)) {
      $fullAddress['full'] = $this->getFullAddressByArray($fullAddress);
    }
    return $fullAddress;
  }

  public function upload(XMLReaderFactory $readerFactory) : void {
    $readerGroup = [
      'levelObj' => $readerFactory::execObjectLevels(),
      'housetype' => $readerFactory::execHousetype(),
      'addhousetype' => $readerFactory::execAddhousetype(),
      'addrObj' => $readerFactory::execAddrObj(),
      'addrObjParams' => $readerFactory::execAddressObjParams(),
      'houses' => $readerFactory::execHouses(),
      'mun' => $readerFactory::execMunHierachi(),
    ];

    foreach ($readerGroup as $name => $reader) {
      $reader->exec($this->tables[$name]);
    }
  }

  protected function getTable(string $name) : QueryModel
  {
    return $this->tables[$name];
  }

  protected function getHouses(string $parentName, string $chiledName) {
    $hierarchy = $this->getTable('mun');

    $objectid = $hierarchy
      ->select(["chiled.objectid"], ['mun' => 'mun_hierarchy'])
      ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
      ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
      ->where('parent.name', '=', $parentName)
      ->andWhere('chiled.name', '=', $chiledName)
      ->save();
//    var_dump($objectid); die();

    // devil write these lines forward
    return $hierarchy
      ->select([
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
      ->where('mun.parentobjid_addr', '=', $objectid[0]['objectid'])
      ->save();
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

  protected function getTrueChiled(string $parent, string $chiled) : array
  {
    $hierarchy = $this->getTable('mun');

    return $hierarchy
      ->select(['chiled.name', 'chiled.typename', 'chiled.id_level'], ['mun' => 'mun_hierarchy'])
      ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
      ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
      ->where('chiled.name', 'LIKE', $chiled . '%')
      ->andWhere('parent.name', 'LIKE', $parent . '%')
      ->save();
  }

  protected function getTrueParent(string $parent, string $trueChiled) : array
  {
    $hierarchy = $this->getTable('mun');

    if (!$hierarchy->nameExist('getTrueParent')) {
      $hierarchy->select(['parent.name', 'parent.typename', 'parent.id_level'], ['mun' => 'mun_hierarchy'])
        ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
        ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
        ->where('chiled.name', '=', $trueChiled)
        ->andWhere('parent.name', 'LIKE', $parent . '%')->name('getTrueParent');
    }

    return $hierarchy->execute([$trueChiled, $parent], 'getTrueParent');
  }

  protected function getLikeParent(string $chiledName) : array
  {
    $hierarchy = $this->getTable('mun');

    return $hierarchy
      ->select(['parent.name', 'parent.typename', 'parent.id_level'], ['mun' => 'mun_hierarchy'])
      ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
      ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.chiledobjid_addr'])
      ->where('chiled.name', '=', $chiledName)
      ->save();
  }
  protected function getLikeAddress(string $halfAddress, bool $onlyTopLevels = true) : array
  {
    $hierarchy = $this->getTable('addrObj');
    $condition = $hierarchy
      ->select(['name', 'typename', 'id_level'])
      ->where('name', 'LIKE', $halfAddress . '%');

    if ($onlyTopLevels) {
      $condition = $condition->andWhere('id_level', '=', '1');
    }

    return $condition
      ->limit(100)
      ->save();
  }
}