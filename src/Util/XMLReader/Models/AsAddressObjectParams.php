<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Models;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Readers\ConcreteReader;

class AsAddressObjectParams extends ConcreteReader 
{
	public static function getElements() : array {
		return ['PARAM'];
	}

	public static function getAttributes() : array {
		return ['OBJECTID', 'TYPEID', 'VALUE'];
	}

	public function execDoWork(QueryModel $model, array $value) : void
	{
    $region = (int)$this->fileFloder;
    $formatted = [
      'objectid_addr' => (int)$value['objectid'],
    ];

    if (!empty($this->getFirstObjectIdAddrObj($model, $formatted['objectid_addr'], $region))) {
      if (in_array($value['typeid'], ['6', '7', '10'])) {
        $type = '';

        switch ($value['typeid']) {
          case '6':
            $formatted['type'] = 'OKATO';
            $formatted['value'] = $value['value'];
            break;
          case '7':
            $formatted['type'] = 'OKTMO';
            $formatted['value'] = $value['value'];
            break;
          case '10':
            $formatted['type'] = 'KLADR';
            $formatted['value'] = $value['value'];
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