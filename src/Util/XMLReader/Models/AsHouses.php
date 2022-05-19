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
		return ['ID', 'OBJECTID', 'OBJECTGUID', 'HOUSENUM', 'HOUSETYPE', 'ISACTUAL', 'ISACTIVE'];
	}

	protected function execDoWork(QueryModel $model, array $value) : void
	{
		if ($value['isactive'] === "1" && $value['isactual'] === "1") {
      $value = array_diff_key($value, array_flip(['isactual', 'isactive']));
      $value['id'] = intval($value['id']);
      $value['objectid'] = intval($value['objectid']);
      $model->forceInsert($value);
		}
	}
}