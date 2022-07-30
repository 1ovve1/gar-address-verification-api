<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\ORM\Table\SQL\QueryModel;
use DB\EntityFactory;
use CLI\XMLParser\Files\XMLFile;

class AS_OBJECT_LEVELS extends XMLFile
{
    public static function getQueryModel(): QueryModel
    {
        return EntityFactory::getObjectLevels();
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

        static::getQueryModel()->forceInsert($values);
    }
}
