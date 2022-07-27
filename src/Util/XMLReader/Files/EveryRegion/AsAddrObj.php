<?php

declare(strict_types=1);

namespace GAR\Util\XMLReader\Files\EveryRegion;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\EntityFactory;
use GAR\Util\XMLReader\Files\XMLFile;

class AsAddrObj extends XMLFile
{
    public static function getQueryModel(): QueryModel
    {
        return EntityFactory::getAddressObjectTable();
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
        $model = static::getQueryModel();
        $region = $this->getIntRegion();

        if (empty($this->getFirstObjectId($model, $values['OBJECTID'], $region))) {
            unset($values['ISACTUAL']); unset($values['ISACTIVE']);

            $values['REGION'] = $region;

            $model->forceInsert($values);
        }
    }

    private function getFirstObjectId(QueryModel $model, int $objectid, int $region): array
    {
        static $name = self::class . 'getFirstObjectId';

        if (!$model->nameExist($name)) {
            $model->select(['objectid'])->where('region', '=', $region)
        ->andWhere('objectid', '=', $objectid)->limit(1)->name($name);
        }

        return $model->execute([$region, $objectid], $name);
    }
}
