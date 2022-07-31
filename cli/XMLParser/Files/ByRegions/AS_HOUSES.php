<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\Houses;
use CLI\XMLParser\Files\XMLFile;

class AS_HOUSES extends XMLFile
{
    public function save(): void
    {
		Houses::save();
    }

    public static function getElement(): string
    {
        return 'HOUSE';
    }

    public static function getAttributes(): array
    {
        return [
            'ISACTUAL' => 'bool',
            'ISACTIVE' => 'bool',
//            'ID' => 'int',
            'OBJECTID' => 'int',
//            'OBJECTGUID' => 'string',
            'HOUSENUM' => 'string',
            'ADDNUM1' => 'string',
            'ADDNUM2' => 'string',
            'HOUSETYPE' => 'int',
            'ADDTYPE1' => 'int',
            'ADDTYPE2' => 'int',
        ];
    }

    public function execDoWork(array &$values): void
    {
        $region = $this->getIntRegion();

        if (empty($this->getFirstObjectId($values['OBJECTID'], $region))) {

            foreach ($this::getAttributes() as $attr => $ignore) {
                $values[$attr] ?? $values[$attr] = null;
            }
            unset($values['ISACTUAL']); unset($values['ISACTIVE']);

            $values['REGION'] = $region;

            Houses::forceInsert($values);
        }
    }

    private function getFirstObjectId(int $objectid, int $region): array
    {
        static $name = self::class . 'getFirstObjectId';

        if (!Houses::nameExist($name)) {
	        Houses::select(['region'])
                ->where('region', $region)
                ->andWhere('objectid', $objectid)
                ->limit(1)
                ->name($name);
        }

        return Houses::execute([$region, $objectid], $name);
    }
}
