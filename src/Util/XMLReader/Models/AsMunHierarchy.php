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
		return ['ID', 'OBJECTID', 'PARENTOBJID', 'OKTMO'];
	}

	public function execDoWork(QueryModel $model, array $value) : void
	{
    if (
      !empty($model->select(['id_level'], ['addr_obj'])
        ->where('objectid', '=', (int)$value['parentobjid'])
        ->save())
      ) {
      $value['id'] = intval($value['id']);
      $value['objectid'] = intval($value['objectid']);
      $value['parentobjid'] = intval($value['parentobjid']);
      $value['oktmo'] = intval($value['oktmo']);
      $model->forceInsert($value);
    }
	}
}