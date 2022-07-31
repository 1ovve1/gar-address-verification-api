<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\ConcreteTable;


class AddrObj extends ConcreteTable 
{
    /**
     * Return fields that need to create in model
     *
     * @return array<string, string>|null
     */
    public function fieldsToCreate(): ?array
    {
        return [
//            'id' =>
//                'INT UNSIGNED NOT NULL',

            'objectid' =>
                'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
        
//            'objectguid' =>
//                'CHAR(36) NOT NULL',
    
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
