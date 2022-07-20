<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Reader\ConcreteReader;

class AsMunHierarchy extends XMLFile
{
	static function getElements() : array {
		return ['ITEM'];
	}

	static function getAttributes() : array {
		return ['OBJECTID', 'PARENTOBJID'];
	}

	function execDoWork(QueryModel $model, array $values) : void
	{
    $region = (int) $this->fileFloder;

    $formatted = [
      'objectid' => (int)$values['objectid'],
      'parentobjid' => (int)$values['parentobjid']
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
          (int)$values['objectid'],
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