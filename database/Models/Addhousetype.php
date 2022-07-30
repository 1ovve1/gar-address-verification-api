<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\ConcreteTable;
use DB\ORM\Table\SQL\QueryModel;

class Addhousetype extends ConcreteTable implements QueryModel
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
              'CHAR(15)',
        
            'disc' =>
              'CHAR(50)',
        ];
    }
}
