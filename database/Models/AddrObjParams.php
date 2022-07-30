<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\ConcreteTable;


class AddrObjParams extends ConcreteTable 
{
    /**
     * Return fields that need to create in model
     *
     * @return array<string, string>|null
     */
    public function fieldsToCreate(): ?array
    {
        return [
            'objectid_addr' =>
        'BIGINT UNSIGNED NOT NULL',

            'type' =>
              'CHAR(5) NOT NULL',

            'value' =>
              'CHAR(31) NOT NULL',

            'region' =>
              'TINYINT UNSIGNED NOT NULL',
        
            'FOREIGN KEY (objectid_addr)' =>
              'REFERENCES addr_obj (objectid)',
        ];
    }
}
