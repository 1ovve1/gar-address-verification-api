<?php

declare(strict_types=1);

namespace CLI\XMLReader\Files\ByRegions;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\EntityFactory;
use CLI\XMLReader\Files\XMLFile;

class AS_HOUSES extends XMLFile
{
    public static function getQueryModel(): QueryModel
    {
        return EntityFactory::getHousesTable();
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
        $model = static::getQueryModel();
        $region = $this->getIntRegion();

        if (empty($this->getFirstObjectId($model, $values['OBJECTID'], $region))) {

            foreach ($this::getAttributes() as $attr => $ignore) {
                $values[$attr] ?? $values[$attr] = null;
            }
            unset($values['ISACTUAL']); unset($values['ISACTIVE']);

            $values['REGION'] = $region;

            $model->forceInsert($values);
        }
    }

    private function getFirstObjectId(QueryModel $model, int $objectid, int $region): array
    {
        static $name = self::class . 'getFirstObjectId';

        if (!$model->nameExist($name)) {
            $model->select(['region'])
                ->where('region', '=', $region)
                ->andWhere('objectid', '=', $objectid)
                ->limit(1)
                ->name($name);
        }

        return $model->execute([$region, $objectid], $name);
    }
}
