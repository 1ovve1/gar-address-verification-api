<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;

class Addhousetype extends ConcreteTable implements QueryModel
{
  public function fieldsToCreate(): ?array
  {
    return [
      'id' => [
        'TINYINT UNSIGNED NOT NULL PRIMARY KEY'
      ],
      'short' => [
        'CHAR(15)'
      ],
      'disc' => [
        'CHAR(50)'
      ],
    ];
  }
}