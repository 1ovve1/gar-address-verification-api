<?php

namespace GAR\Util\XMLReader\Files\Single;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\EntityFactory;
use GAR\Util\XMLReader\Files\XMLFile;

class AsHouseTypes extends XMLFile
{
  static function getQueryModel(): QueryModel
  {
    return EntityFactory::getHousetype();
  }

  /**
   * return elements of xml document
   * @return string elements names
   */
  static function getElement(): string
  {
    return 'HOUSETYPE';
  }

  /**
   * return attributes of elements in xml document
   * @return array attributes names
   */
  static function getAttributes(): array
  {
    return ['ID', 'NAME', 'SHORTNAME'];
  }

  /**
   * procedure that contains main operations from exec method
   * @param array $values current parse element
   * @return void
   */
  function execDoWork(array $values): void
  {
    static::getQueryModel()->forceInsert([
      (int)$values['ID'],
      $values['SHORTNAME'],
      $values['NAME'],
    ]);
  }

}