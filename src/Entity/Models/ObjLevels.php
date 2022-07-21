<?php

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;

class ObjLevels extends ConcreteTable implements QueryModel
{
    /**
     * Return fields that need to create in model
     *
     * @return array<string, string>|null
     */
    public function fieldsToCreate(): ?array
    {
        return [
      'id' =>
        'TINYINT UNSIGNED NOT NULL PRIMARY KEY',
        
      'disc' =>
        'CHAR(70)',
    ];
    }
}
