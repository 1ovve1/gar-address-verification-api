<?php

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;
use JetBrains\PhpStorm\ArrayShape;

class ObjLevels extends ConcreteTable implements QueryModel
{
  #[ArrayShape(['id' => "string[]", 'disc' => "string[]"])]
  public function fieldsToCreate(): ?array
  {
    return [
      'id' => [
        'TINYINT UNSIGNED NOT NULL'
      ],
      'disc' => [
        'CHAR(70)'
      ]
    ];
  }
}