<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Models;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Readers\ConcreteReader;

class AsAddressObject extends ConcreteReader
{
	public static function getElements() : array {
		return ['OBJECT'];
	}

	public static function getAttributes() : array {
		return ['ID', 'OBJECTID', 'OBJECTGUID', 'NAME', 'TYPENAME', 'LEVEL', 'ISACTUAL', 'ISACTIVE'];
	}

	public function execDoWork(QueryModel $model, array $value) : void
	{
		if ($value['isactive'] === "1" && $value['isactual'] === "1") {
      $region = (int)$this->fileFloder;

      if (empty($this->getFirstObjectId($model, (int)$value['objectid'], $region))) {
        $model->forceInsert([
          (int)$value['id'],
          (int)$value['objectid'],
          $value['objectguid'],
          (int)$value['level'],
          $value['name'],
          $value['typename'],
          (int)$region,
        ]);
      }
		}
	}

  private function getFirstObjectId(QueryModel $model, int $objectid, int $region) : array
  {
    static $name = self::class . 'getFirstObjectId';

    if (!$model->nameExist($name)) {
      $model->select(['objectid'])->where('region', '=', $region)
        ->andWhere('objectid', '=', $objectid)->limit(1)->name($name);
    }

    return $model->execute([$region, $objectid], $name);
  }
}