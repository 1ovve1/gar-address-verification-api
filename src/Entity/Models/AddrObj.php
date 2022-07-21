<?php

declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;

class AddrObj extends ConcreteTable implements QueryModel
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
                'INT UNSIGNED NOT NULL',

            'objectid' =>
                'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
        
            'objectguid' =>
                'CHAR(36) NOT NULL',
    
      'id_level' =>
        'TINYINT UNSIGNED NOT NULL',

            'name' =>
                'VARCHAR(255) NOT NULL',
        
            'typename' =>
                'VARCHAR(31) NOT NULL',

      'region' =>
        'TINYINT UNSIGNED NOT NULL',
    
      'FOREIGN KEY (id_level)' =>
        'REFERENCES obj_levels (id)',
        ];
    }
}
