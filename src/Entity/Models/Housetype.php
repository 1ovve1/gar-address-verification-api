<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;
use JetBrains\PhpStorm\ArrayShape;

class Housetype extends ConcreteTable implements QueryModel
{
  #[ArrayShape(['id' => "string[]", 'short' => "string[]", 'desc' => "string[]"])]
  public function fieldsToCreate(): ?array
  {
    return [
      'id' => [
        'TINYINT UNSIGNED NOT NULL PRIMARY KEY',
      ],
      'short' => [
        'CHAR(15) NOT NULL',
      ],
      'disc' => [
        'CHAR(50) NOT NULL',
      ],
    ];
  }
}