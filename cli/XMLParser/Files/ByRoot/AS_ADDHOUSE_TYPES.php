<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\Addhousetype;
use CLI\XMLParser\Files\XMLFile;

class AS_ADDHOUSE_TYPES extends XMLFile
{
    public function save(): void
    {
        Addhousetype::save();
    }

    /**
     * return elements of xml document
     * @return string elements names
     */
    public static function getElement(): string
    {
        return 'HOUSETYPE';
    }

    /**
     * return attributes of elements in xml document
     * @return array attributes names
     */
    public static function getAttributes(): array
    {
        return [
            'ID' => 'int',
            'SHORTNAME' => 'string',
            'NAME' => 'string',
        ];
    }

    /**
     * procedure that contains main operations from exec method
     * @param array $values current parse element
     * @return void
     */
    public function execDoWork(array &$values): void
    {
        Addhousetype::forceInsert($values);
    }
}
