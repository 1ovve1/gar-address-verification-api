<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use CLI\XMLParser\Files\XMLFile;
use DB\Models\AddrObjParams;

class AS_ADDR_OBJ_PARAMS extends XMLFile
{
    public function save(): void
    {
		AddrObjParams::save();
    }

    public static function getElement(): string
    {
        return 'PARAM';
    }

    public static function getAttributes(): array
    {
        return [
            'OBJECTID' => 'int',
            'TYPEID' => 'int',
            'VALUE' => 'string',
        ];
    }

    public function execDoWork(array &$values): void
    {
        $region = $this->getIntRegion();

        if (in_array($values['TYPEID'], [6, 7, 10], true)) {
            if (!empty($this->getFirstObjectIdAddrObj($values['OBJECTID'], $region))) {
                $values['TYPEID'] = match ($values['TYPEID']) {
                    6 => 'OKATO',
                    7 => 'OKTMO',
                    10 => 'KLADR',
                };

                $values['REGION'] = $region;

                AddrObjParams::forceInsert($values);
            }
        }
    }


    private function getFirstObjectIdAddrObj(int $objectid, int $region): array
    {
        static $name = self::class . 'getFirstObjectIdAddrObj';

        if (!AddrObjParams::nameExist($name)) {
	        AddrObjParams::select(['objectid'], ['addr_obj'])
                ->where('region', $region)
                ->andWhere('objectid', $objectid)
                ->limit(1)
                ->name($name);
        }

        return AddrObjParams::execute([$region, $objectid], $name);
    }
}
