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
		return ['OBJECTID', 'TYPEID', 'VALUE'];
	}

	function execDoWork(array $values) : void
	{
    $region = $this->getIntRegion();
    $model = static::getQueryModel();

    $formatted = [
      'OBJECTID_ADDR' => (int)$values['OBJECTID'],
    ];

    if (!empty($this->getFirstObjectIdAddrObj($model, $formatted['OBJECTID_ADDR'], $region))) {
      if (in_array($values['TYPEID'], ['6', '7', '10'])) {
        $type = '';

        switch ($values['TYPEID']) {
          case '6':
            $formatted['TYPE'] = 'OKATO';
            $formatted['VALUE'] = $values['VALUE'];
            break;
          case '7':
            $formatted['TYPE'] = 'OKTMO';
            $formatted['VALUE'] = $values['VALUE'];
            break;
          case '10':
            $formatted['TYPE'] = 'KLADR';
            $formatted['VALUE'] = $values['VALUE'];
            break;
        }


        $model->forceInsert($formatted + ['region' => $region]);

//        if (empty($this->getFirstObjectId($model, $formatted['objectid_addr'], $region))) {
//          $this->doInsert($model, $type, $formatted);
//        } else {
//          $this->doUpdate($model, $type, $formatted);
//        }
      }
    }
	}



  private function doUpdate(QueryModel $model, string $type, array $formatted) : void
  {
    if (!$model->nameExist($type . 'U')) {
      $model->update($type, $formatted[$type])
        ->where('objectid_addr', '=', $formatted['objectid_addr'])
        ->name($type . 'U');
    }

    $model->execute([$formatted[$type], $formatted['objectid_addr']], $type . 'U');
  }

  private function doInsert(QueryModel $model, string $type,  array $formatted) : void
  {
    if (!$model->nameExist($type . 'I')) {
      $model->insert($formatted)
        ->name($type . 'I');
    }

    $model->execute(array_values($formatted), $type . 'I');
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