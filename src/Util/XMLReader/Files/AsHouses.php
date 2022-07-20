<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Reader\ConcreteReader;

class AsHouses extends XMLFile
{
	public static function getElements() : array {
		return ['HOUSE'];
	}

	public static function getAttributes() : array {
		return [
      'ID', 'OBJECTID', 'OBJECTGUID', 'HOUSENUM',
      'ADDNUM1', 'ADDNUM2', 'HOUSETYPE', 'ADDTYPE1',
      'ADDTYPE2', 'ISACTUAL', 'ISACTIVE'
    ];
	}

	function execDoWork(QueryModel $model, array $values) : void
	{
		if ($values['isactive'] === "1" && $values['isactual'] === "1") {
      $region = (int)$this->fileFloder;

      if (empty($this->getFirstObjectId($model, (int)$values['objectid'], $region))) {
        $model->forceInsert([
          (int)$values['id'],
          (int)$values['objectid'],
          $values['objectguid'],
          $values['housenum'],
          $values['addnum1'],
          $values['addnum2'],
          $values['housetype'],
          $values['addtype1'],
          $values['addtype2'],
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