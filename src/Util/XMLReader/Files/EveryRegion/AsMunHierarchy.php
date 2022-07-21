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
		return [
      'OBJECTID' => 'int', 
      'PARENTOBJID' => 'int',
    ];
	}

	function execDoWork(array $values) : void
	{
    $region = $this->getIntRegion();
    $model = static::getQueryModel();

    if (isset($values['PARENTOBJID']) && !empty($this->getIdAddrObj($model, $values['PARENTOBJID'], $region))) {
      if (!empty($this->getIdAddrObj($model, $values['OBJECTID'], $region))) {
        $model->forceInsert([
          $values['PARENTOBJID'],
          $values['OBJECTID'],
          null,
        ]);
      } else if (!empty($this->getIdHouses($model, $values['OBJECTID'], $region))) {
        $model->forceInsert([
          $values['PARENTOBJID'],
          null,
          $values['OBJECTID'],
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