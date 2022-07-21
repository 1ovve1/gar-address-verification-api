<?php

declare(strict_types=1);

namespace GAR\Util\XMLReader\Files\EveryRegion;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\EntityFactory;
use GAR\Util\XMLReader\Files\XMLFile;

class AsAddrObjParams extends XMLFile
{
    public static function getQueryModel(): QueryModel
    {
        return EntityFactory::getAddressObjectParamsTable();
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

    public function execDoWork(array $values): void
    {
        $region = $this->getIntRegion();
        $model = static::getQueryModel();

        if (in_array($values['TYPEID'], [6, 7, 10], true)) {
            if (!empty($this->getFirstObjectIdAddrObj($model, $values['OBJECTID'], $region))) {
                $values['TYPEID'] = match ($values['TYPEID']) {
                    6 => 'OKATO',
                    7 => 'OKTMO',
                    10 => 'KLADR',
                };


                $model->forceInsert([
                    $values['OBJECTID'],
                    $values['TYPEID'],
                    $values['VALUE'],
                    $region,
                ]);
            }
        }
    }


    private function getFirstObjectIdAddrObj(QueryModel $model, int $objectid, int $region): array
    {
        static $name = self::class . 'getFirstObjectIdAddrObj';

        if (!$model->nameExist($name)) {
            $model->select(['objectid'], ['addr_obj'])->where('region', '=', $region)
        ->andWhere('objectid', '=', $objectid)->limit(1)->name($name);
        }

        return $model->execute([$region, $objectid], $name);
    }
}
