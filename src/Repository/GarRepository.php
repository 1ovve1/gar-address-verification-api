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
}