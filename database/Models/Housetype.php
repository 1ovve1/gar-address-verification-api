<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\ConcreteTable;


class Housetype extends ConcreteTable 
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
