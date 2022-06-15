<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Models;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Readers\ConcreteReader;

class AsMunHierarchy extends ConcreteReader
{
	public static function getElements() : array {
		return ['ITEM'];
	}

	public static function getAttributes() : array {
		return ['OBJECTID', 'PARENTOBJID'];
	}

	public function execDoWork(QueryModel $model, array $value) : void
	{
    $region = (int) $this->fileFloder;

    $formatted = [
      'objectid' => (int)$value['objectid'],
      'parentobjid' => (int)$value['parentobjid']
    ];

    if (!empty($this->getIdAddrObj($model, $formatted['parentobjid'], $region))) {
      if (!empty($this->getIdAddrObj($model, $formatted['objectid'], $region))) {
        $model->forceInsert([
          $formatted['parentobjid'],
          $formatted['objectid'],
          null,
        ]);
      } else if (!empty($this->getIdHouses($model, $formatted['objectid'], $region))) {
        $model->forceInsert([
          $formatted['parentobjid'],
          null,
          (int)$value['objectid'],
        ]);
      }
    }
	}

  private function getIdAddrObj(QueryModel $model, int $objectid, int $region) : array
  {
    static $name = self::class . 'getIdAddrObj';

    if (!$model->nameExist($name)) {
      $model->select(['id'], ['addr_obj'])->where('region', '=', $region)
        ->andWhere('objectid', '=', $objectid)->limit(1)->name($name);
    }

    return $model->execute([$region, $objectid], $name);
  }

  private function getIdHouses(QueryModel $model, int $objectid, int $region) : array
  {
    static $name = self::class . 'getFirstObjectIdHouses';

    if (!$model->nameExist($name)) {
      $model->select(['id'], ['houses'])->where('region', '=', $region)
        ->andWhere('objectid', '=', $objectid)->limit(1)->name($name);
    }

    return $model->execute([$region, $objectid], $name);
  }
}