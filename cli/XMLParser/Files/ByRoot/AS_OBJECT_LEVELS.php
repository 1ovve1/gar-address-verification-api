<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\ObjLevels;
use CLI\XMLParser\Files\XMLFile;

class AS_OBJECT_LEVELS extends XMLFile
{
    public function save(): void
    {
        ObjLevels::save();
    }

    /**
     * return elements of xml document
     * @return string elements names
     */
    public static function getElement(): string
    {
        return 'OBJECTLEVEL';
    }

    /**
     * return attributes of elements in xml document
     * @return array attributes names
     */
    public static function getAttributes(): array
    {
        return [
            'LEVEL' => 'int',
            'NAME' => 'string',
            'ISACTIVE' => 'bool',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function execDoWork(array &$values): void
    {
        unset($values['ISACTIVE']);

        ObjLevels::forceInsert($values);
    }
}
