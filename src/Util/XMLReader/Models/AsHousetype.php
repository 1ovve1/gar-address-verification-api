<?php

namespace GAR\Util\XMLReader\Models;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Readers\ConcreteReader;

class AsHousetype extends ConcreteReader
{
  /**
   * return elements of xml document
   * @return array elements names
   */
  static function getElements(): array
  {
    return ['HOUSETYPE'];
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
   * @param QueryModel $model table model
   * @param array $value current parse element
   * @return void
   */
  protected function execDoWork(QueryModel $model, array $value): void
  {
    $model->forceInsert([
      (int)$value['id'],
      $value['shortname'],
      $value['name'],
    ]);
  }

}