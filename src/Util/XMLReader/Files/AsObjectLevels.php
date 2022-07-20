<?php

namespace GAR\Util\XMLReader\Files;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Reader\ConcreteReader;

class AsObjectLevels extends XMLFile
{
  /**
   * return elements of xml document
   * @return array elements names
   */
  static function getElements(): array
  {
    return ['OBJECTLEVEL'];
  }

  /**
   * return attributes of elements in xml document
   * @return array attributes names
   */
  static function getAttributes(): array
  {
    return ['LEVEL', 'NAME', 'ISACTIVE'];
  }

  /**
   * procedure that contains main operations from exec method
   * @param QueryModel $model table model
   * @param array $values current parse element
   * @return void
   */
  function execDoWork(QueryModel $model, array $values): void
  {
    if ($values['isactive'] == 'true') {
      $model->forceInsert([
        (int)$values['level'],
        $values['name']
      ]);
    }
  }

}