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
      'levelObj' => $tableFactory::getObjectLevels(),
      'addrObj' => $tableFactory::getAddressObjectTable(),
      'addrObjParams' => $tableFactory::getAddressObjectParamsTable(),
      'houses' => $tableFactory::getHousesTable(),
//      'admin' => $tableFactory::getAdminTable(),
      'mun' => $tableFactory::getMunTable(),
    ];
  }

  public function getLikeAddress(array $halfAddress) : array
  {
    $hierarchy = $this->getTable('mun');
    $chiled = array_pop($halfAddress);

    $fullAddress = [];

    foreach (array_reverse($halfAddress) as $parent) {
      if (empty($fullAddress)) {
        $fullAddress[$chiled] = $this->getTrueChiled($parent, $chiled);
        $chiled = end($fullAddress)[0]['name_addr'];
      }
      $fullAddress[$parent] = $this->getTrueParent($parent, $chiled);
      $chiled = end($fullAddress)[0]['name_addr'];
    }
    $fullAddress['full'] = $this->getFullAddressByArray($fullAddress);
    return $fullAddress;
  }

  public function upload(XMLReaderFactory $readerFactory) : void {
    $readerGroup = [
      'levelObj' => $readerFactory::execObjectLevels(),
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

  protected function getFullAddressByArray(array $address) : string
  {
    $formatted = [];
    foreach (array_reverse($address) as $contains) {
      if (count($contains) > 1) {
        continue;
      }
      foreach ($contains as $elem) {
        if (
          array_key_exists('name_addr', $elem) && array_key_exists('typename_addr', $elem)
        ) {
          $formatted[] = implode(' ', [$elem['typename_addr'], $elem['name_addr']]);
        }
      }
    }

    return implode(', ', $formatted);
  }

  protected function getTrueChiled(string $parent, string $chiled) : array
  {
    $hierarchy = $this->getTable('mun');

    return $hierarchy
      ->select(['chiled.name_addr', 'chiled.typename_addr', 'chiled.level_addr'], ['mun' => 'mun_hierarchy'])
      ->innerJoin('addr_obj as parent', ['parent.objectid_addr' => 'mun.parentobjid_mun'])
      ->innerJoin('addr_obj as chiled', ['chiled.objectid_addr' => 'mun.objectid_mun'])
      ->where('chiled.name_addr', 'LIKE', $chiled . '%')
      ->andWhere('parent.name_addr', 'LIKE', $parent . '%')
      ->save();
  }

  protected function getTrueParent(string $parent, string $trueChiled) : array
  {
    $hierarchy = $this->getTable('mun');

    return $hierarchy
      ->select(['parent.name_addr', 'chiled.typename_addr', 'parent.level_addr'], ['mun' => 'mun_hierarchy'])
      ->innerJoin('addr_obj as parent', ['parent.objectid_addr' => 'mun.parentobjid_mun'])
      ->innerJoin('addr_obj as chiled', ['chiled.objectid_addr' => 'mun.objectid_mun'])
      ->where('chiled.name_addr', '=', $trueChiled)
      ->andWhere('parent.name_addr', 'LIKE', $parent . '%')
      ->save();
  }
}