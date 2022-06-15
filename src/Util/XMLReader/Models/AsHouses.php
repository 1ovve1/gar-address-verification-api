<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Models;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Readers\ConcreteReader;

class AsHouses extends ConcreteReader 
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

	protected function execDoWork(QueryModel $model, array $value) : void
	{
		if ($value['isactive'] === "1" && $value['isactual'] === "1") {
      $region = (int)$this->fileFloder;

      if (empty($this->getFirstObjectId($model, (int)$value['objectid'], $region))) {
        $model->forceInsert([
          (int)$value['id'],
          (int)$value['objectid'],
          $value['objectguid'],
          $value['housenum'],
          $value['addnum1'],
          $value['addnum2'],
          $value['housetype'],
          $value['addtype1'],
          $value['addtype2'],
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