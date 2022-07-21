<?php

declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;

class MunHierarchy extends ConcreteTable implements QueryModel
{
    /**
     * Return fields that need to create in model
     *
     * @return array<string, string>|null
     */
    public function fieldsToCreate(): ?array
    {
        return [
            'parentobjid_addr' =>
                'BIGINT UNSIGNED NOT NULL',

            'chiledobjid_addr' =>
              'BIGINT UNSIGNED',

            'chiledobjid_houses' =>
              'BIGINT UNSIGNED',

            'FOREIGN KEY (parentobjid_addr)' =>
              'REFERENCES addr_obj (objectid)',

            'FOREIGN KEY (chiledobjid_addr)' =>
              'REFERENCES addr_obj (objectid)',

            'FOREIGN KEY (chiledobjid_houses)' =>
              'REFERENCES houses (objectid)',
        ];
    }
}
