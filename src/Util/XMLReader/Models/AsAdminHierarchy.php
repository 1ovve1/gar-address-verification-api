<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Models;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Readers\ConcreteReader;

class AsAdminHierarchy extends ConcreteReader
{
	public static function getElements() : array {
		return ['ITEM'];
	}

	public static function getAttributes() : array {
		return ['ID', 'OBJECTID', 'PARENTOBJID'];
	}

	public function execDoWork(QueryModel $model, array $value) : void
	{
//    if (
//      $model->select(['addr.objectid_addr'], ['addr' => 'addr_obj'])
//        ->where('addr.objectid_addr', '=', $value['objectid'])
//        ->save()
//    ) {
//      if (
//        $model->select(['addr.objectid_addr'], ['addr' => 'addr_obj'])
//        ->where('addr.objectid_addr', '=', $value['parentobjid'])
//        ->save()
//        ){
        $model->forceInsert([
          (int)$value['id'],
          (int)$value['objectid'],
          (int)$value['parentobjid'],
        ]);
//      } else {
//        Log::write('not found ' . (int)$value['parentobjid'] . ' in ' . $this->key());
//      }
//    } else {
//      Log::write('not found ' . (int)$value['objectid'] . ' in ' . $this->key());
//    }
	}
}