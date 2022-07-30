<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\Housetype;
use CLI\XMLParser\Files\XMLFile;

class AS_HOUSE_TYPES extends XMLFile
{
    public function save(): void
    {
        Housetype::save();
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
        Housetype::forceInsert($values);
    }
}
