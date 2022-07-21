<?php

declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;

class Housetype extends ConcreteTable implements QueryModel
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
        
            'short' =>
              'CHAR(15) NOT NULL',
        
            'disc' =>
              'CHAR(50) NOT NULL',
        ];
    }
}
