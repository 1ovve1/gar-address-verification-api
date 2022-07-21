<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files\EveryRegion;

use GAR\Entity\EntityFactory;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Files\XMLFile;

class AsAddrObjParams extends XMLFile
{
  static function getQueryModel(): QueryModel
  {
    return EntityFactory::getAddressObjectParamsTable();
  }

	static function getElement() : string {
		return 'PARAM';
	}

	static function getAttributes() : array {
		return [
      'OBJECTID' => 'int', 
      'TYPEID' => 'int', 
      'VALUE' => 'string',
    ];
	}

	function execDoWork(array $values) : void
	{
    $region = $this->getIntRegion();
    $model = static::getQueryModel();

    if (in_array($values['TYPEID'], [6, 7, 10])) {
      if (!empty($this->getFirstObjectIdAddrObj($model, $values['OBJECTID'], $region))) {
        $type = '';

        switch ($values['TYPEID']) {
          case 6:
            $values['TYPEID'] = 'OKATO';
            $values['VALUE'] = $values['VALUE'];
            break;
          case 7:
            $values['TYPEID'] = 'OKTMO';
            $values['VALUE'] = $values['VALUE'];
            break;
          case '10':
            $values['TYPEID'] = 'KLADR';
            $values['VALUE'] = $values['VALUE'];
            break;
        }


        $model->forceInsert($values + [$region]);
      }
    }
	}

  
  private function getFirstObjectId(QueryModel $model, int $objectid, int $region) : array
  {
    static $name = self::class . 'getFirstObjectId';

    if (!$model->nameExist($name)) {
      $model->select(['objectid_addr'])->where('region', '=', $region)
        ->andWhere('objectid_addr', '=', $objectid)->limit(1)->name($name);
    }

    return $model->execute([$region, $objectid], $name);
  }


  private function getFirstObjectIdAddrObj(QueryModel $model, int $objectid, int $region) : array
  {
    static $name = self::class . 'getFirstObjectIdAddrObj';

    if (!$model->nameExist($name)) {
      $model->select(['objectid'], ['addr_obj'])->where('region', '=', $region)
        ->andWhere('objectid', '=', $objectid)->limit(1)->name($name);
    }

    return $model->execute([$region, $objectid], $name);
  }
}