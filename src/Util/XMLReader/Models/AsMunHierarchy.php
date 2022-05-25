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
		return ['ID', 'OBJECTID', 'PARENTOBJID'];
	}

	public function execDoWork(QueryModel $model, array $value) : void
	{
    if (!empty($model->findFirst('objectid', (int)$value['parentobjid'], 'addr_obj'))) {
      if (!empty($model->findFirst('objectid', (int)$value['objectid'], 'addr_obj'))) {
        $model->forceInsert([
          (int)$value['id'],
          (int)$value['parentobjid'],
          (int)$value['objectid'],
          null,
        ]);
      } else if (!empty($model->findFirst('objectid', (int)$value['objectid'], 'houses'))) {
        $model->forceInsert([
          (int)$value['id'],
          (int)$value['parentobjid'],
          null,
          (int)$value['objectid'],
        ]);
      }
    }
	}
}