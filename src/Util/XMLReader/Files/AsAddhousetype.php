<?php

namespace GAR\Util\XMLReader\Files;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Reader\ConcreteReader;

class AsAddhousetype extends XMLFile
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
   * @param array $values current parse element
   * @return void
   */
  function execDoWork(QueryModel $model, array $values): void
  {
    $model->forceInsert([
      (int)$values['id'],
      $values['shortname'],
      $values['name'],
    ]);
  }

}