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
        $chiled = end($fullAddress)[0]['name'];
      }
      $fullAddress[$parent] = $this->getTrueParent($parent, $chiled);
      $chiled = end($fullAddress)[0]['name'];
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
      ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.objectid'])
      ->where('chiled.name', 'LIKE', $chiled . '%')
      ->andWhere('parent.name', 'LIKE', $parent . '%')
      ->save();
  }

  protected function getTrueParent(string $parent, string $trueChiled) : array
  {
    $hierarchy = $this->getTable('mun');

    return $hierarchy
      ->select(['parent.name', 'chiled.typename', 'parent.id_level'], ['mun' => 'mun_hierarchy'])
      ->innerJoin('addr_obj as parent', ['parent.objectid' => 'mun.parentobjid_addr'])
      ->innerJoin('addr_obj as chiled', ['chiled.objectid' => 'mun.objectid'])
      ->where('chiled.name', '=', $trueChiled)
      ->andWhere('parent.name', 'LIKE', $parent . '%')
      ->save();
  }
}