<?php

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;

class ObjLevels extends ConcreteTable implements QueryModel
{
  public function fieldsToCreate(): ?array
  {
    return [
      'id' => [
        'TINYINT UNSIGNED NOT NULL PRIMARY KEY'
      ],
      'disc' => [
        'CHAR(70)'
      ]
    ];
  }
}