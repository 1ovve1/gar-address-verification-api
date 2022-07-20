<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files\EveryRegion;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\EntityFactory;
use GAR\Util\XMLReader\Files\XMLFile;
use GAR\Util\XMLReader\Reader\ConcreteReader;

class AsMunHierarchy extends XMLFile
{
  static function getQueryModel(): QueryModel
  {
    return EntityFactory::getMunTable();
  }

	static function getElement() : string {
		return 'ITEM';
	}

	static function getAttributes() : array {
		return ['OBJECTID', 'PARENTOBJID'];
	}

	function execDoWork(array $values) : void
	{
    $region = $this->getIntRegion();
    $model = static::getQueryModel();

    $formatted = [
      'OBJECTID' => (int)$values['OBJECTID'],
      'PARENTOBJID' => (int)$values['PARENTOBJID']
    ];

    if (!empty($this->getIdAddrObj($model, $formatted['PARENTOBJID'], $region))) {
      if (!empty($this->getIdAddrObj($model, $formatted['OBJECTID'], $region))) {
        $model->forceInsert([
          $formatted['PARENTOBJID'],
          $formatted['OBJECTID'],
          null,
        ]);
      } else if (!empty($this->getIdHouses($model, $formatted['OBJECTID'], $region))) {
        $model->forceInsert([
          $formatted['PARENTOBJID'],
          null,
          (int)$values['OBJECTID'],
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