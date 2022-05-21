<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Models;

use GAR\Database\Table\SQL\QueryModel;
use GAR\Util\XMLReader\Readers\ConcreteReader;

class AsAddressObjectParams extends ConcreteReader 
{
	public static function getElements() : array {
		return ['PARAM'];
	}

	public static function getAttributes() : array {
		return ['ID', 'OBJECTID', 'TYPEID', 'VALUE'];
	}

	public function execDoWork(QueryModel $model, array $value) : void
	{
    $formatted = [
      'id' => (int)$value['id'],
      'objectid_addr' => (int)$value['objectid'],
    ];

    if (empty($model->findFirst('objectid', $formatted['objectid_addr'], 'addr_obj'))) {
      return;
    }

    if (in_array($value['typeid'], ['6', '7', '11'])) {
      $type = '';

      switch($value['typeid']) {
        case '6':
          $type = 'OKATO';
          $formatted[$type] = (int)$value['value'];
          break;
        case '7':
          $type = 'OKTMO';
          $formatted[$type] = (int)$value['value'];
          break;
        case '11':
          $type = 'KLADR';
          $formatted[$type] = (int)$value['value'];
          break;
      }

      if (empty($model->findFirst('objectid_addr', $formatted['objectid_addr']))) {
        $model->insert($formatted)->save();
      } else {
        $model->update($type, $formatted[$type])
          ->where('objectid_addr', '=', $formatted['objectid_addr'])
          ->save();
      }
    }
	}
}