<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Files\EveryRegion;

use GAR\Entity\EntityFactory;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Files\XMLFile;

class AsAddrObj extends XMLFile
{
  static function getQueryModel(): QueryModel
  {
    return EntityFactory::getAddressObjectTable();
  }

  static function getElement() : string {
		return 'OBJECT';
	}

	static function getAttributes() : array {
		return ['ID', 'OBJECTID', 'OBJECTGUID', 'NAME', 'TYPENAME', 'LEVEL', 'ISACTUAL', 'ISACTIVE'];
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
          (int)$values['LEVEL'],
          $values['NAME'],
          $values['TYPENAME'],
          $region,
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