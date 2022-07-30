<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\AddrObj;
use CLI\XMLParser\Files\XMLFile;

class AS_ADDR_OBJ extends XMLFile
{
    public function save(): void
    {
        AddrObj::save();
    }

    public static function getElement(): string
    {
        return 'OBJECT';
    }

    public static function getAttributes(): array
    {
        return [
            'ISACTUAL' => 'bool',
            'ISACTIVE' => 'bool',
//            'ID' => 'int',
            'OBJECTID' => 'int',
//            'OBJECTGUID' => 'string',
            'LEVEL' => 'int',
            'NAME' => 'string',
            'TYPENAME' => 'string',
        ];
    }

    public function execDoWork(array &$values): void
    {
        $region = $this->getIntRegion();

        if (empty($this->getFirstObjectId($values['OBJECTID'], $region))) {
            unset($values['ISACTUAL']); unset($values['ISACTIVE']);

            $values['REGION'] = $region;

            AddrObj::forceInsert($values);
        }
    }

    private function getFirstObjectId(int $objectid, int $region): array
    {
        static $name = self::class . 'getFirstObjectId';

        if (!AddrObj::nameExist($name)) {
            AddrObj::select(['objectid'])
                ->where('region', $region)
                ->andWhere('objectid', $objectid)
                ->limit(1)
                ->name($name);
        }

        return AddrObj::execute([$region, $objectid], $name);
    }
}
