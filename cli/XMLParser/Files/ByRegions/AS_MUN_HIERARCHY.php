<?php declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\MunHierarchy;
use CLI\XMLParser\Files\XMLFile;

class AS_MUN_HIERARCHY extends XMLFile
{
    public function save(): void
    {
        MunHierarchy::save();
    }

    public static function getElement(): string
    {
        return 'ITEM';
    }

    public static function getAttributes(): array
    {
        return [
            'OBJECTID' => 'int',
            'PARENTOBJID' => 'int',
        ];
    }

    public function execDoWork(array &$values): void
    {
        $region = $this->getIntRegion();

        if (isset($values['PARENTOBJID']) && !empty($this->getIdAddrObj($values['PARENTOBJID'], $region))) {
            if (!empty(self::getIdAddrObj($values['OBJECTID'], $region))) {
                MunHierarchy::forceInsert([
                    $values['PARENTOBJID'],
                    $values['OBJECTID'],
                    null,
	                $region,
                ]);
            } elseif (!empty(self::getIdHouses($values['OBJECTID'], $region))) {
                MunHierarchy::forceInsert([
                    $values['PARENTOBJID'],
                    null,
                    $values['OBJECTID'],
	                $region,
                ]);
            }
        }
    }

    private static function getIdAddrObj(int $objectid, int $region): array
    {
        static $name = self::class . 'getIdAddrObj';

        if (!MunHierarchy::nameExist($name)) {
            MunHierarchy::select(['region'], ['addr_obj'])
                ->where('region', $region)
                ->andWhere('objectid', $objectid)
                ->limit(1)
                ->name($name);
        }

        return MunHierarchy::execute([$region, $objectid], $name);
    }

    private static function getIdHouses(int $objectid, int $region): array
    {
        static $name = self::class . 'getFirstObjectIdHouses';

        if (!MunHierarchy::nameExist($name)) {
	        MunHierarchy::select(['region'], ['houses'])
            ->where('region', $region)
            ->andWhere('objectid', $objectid)
            ->limit(1)
            ->name($name);
        }

        return MunHierarchy::execute([$region, $objectid], $name);
    }
}
