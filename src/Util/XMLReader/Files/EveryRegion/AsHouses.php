<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files\EveryRegion;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Entity\EntityFactory;
use GAR\Util\XMLReader\Files\XMLFile;
use GAR\Util\XMLReader\Reader\ConcreteReader;

class AsHouses extends XMLFile
{
  static function getQueryModel(): QueryModel
  {
    return EntityFactory::getHousesTable();
  }

	public static function getElement() : string {
		return 'HOUSE';
	}

	public static function getAttributes() : array {
		return [
      'ID', 'OBJECTID', 'OBJECTGUID', 'HOUSENUM',
      'ADDNUM1', 'ADDNUM2', 'HOUSETYPE', 'ADDTYPE1',
      'ADDTYPE2', 'ISACTUAL', 'ISACTIVE'
    ];
	}

	function execDoWork(array $values) : void
	{
		if ($values['ISACTIVE'] === "1" && $values['ISACTUAL'] === "1") {
      $model = static::getQueryModel();
      $region = $this->getIntRegion();

      if (empty($this->getFirstObjectId($model, (int)$values['OBJECTID'], $region))) {

        $model->forceInsert([
          (int)$values['ID'],
          (int)$values['OBJECTID'],
          $values['OBJECTGUID'],
          $values['HOUSENUM'] ?? null,
          $values['ADDNUM1'] ?? null,
          $values['ADDNUM2'] ?? null,
          $values['HOUSETYPE'] ?? null,
          $values['ADDTYPE1'] ?? null,
          $values['ADDTYPE2'] ?? null,
          $region
        ]);
      }
    }
	}

  private function getFirstObjectId(QueryModel $model, int $objectid, int $region) : array
  {
    static $name = self::class . 'getFirstObjectId';

    if (!$model->nameExist($name)) {
      $model->select(['id'])->where('region', '=', $region)
        ->andWhere('objectid', '=', $objectid)->limit(1)->name($name);
    }

    return $model->execute([$region, $objectid], $name);
  }
}