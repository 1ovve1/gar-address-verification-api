<?php declare(strict_types=1);

namespace GAR\Repository;


use GAR\Database\Table\SQL\QueryModel;
use GAR\Repository\Codes;

class AddressByCodeRepository extends BaseRepo
{

  public function getCode(int $objectId, string $type) : ?array
  {
    $code = null;

    if (Codes::tryFrom($type)) {
      if (Codes::from($type) === Codes::ALL) {
        $code = $this->getAllCodesByObjectId($objectId);
      } else {
        $code = $this->getCodeByObjectId($objectId, $type);
      }
    }
    return $code;
  }

  public function getCodeByObjectId(int $objectId, string $type) : array
  {
    $params = $this->getDatabase();

    if (!$params->nameExist('getCode' . $type)) {
      $fmt = strtoupper($type);
      $params->select(["params.{$fmt}"], ['params' => 'addr_obj_params'])
        ->where('params.objectid_addr', '=', $objectId)
        ->name('getCode' . $type);
    }

    return $params->execute([$objectId], 'getCode' . $type);
  }

  public function getAllCodesByObjectId(int $objectId) : array
  {
    $params = $this->getDatabase();

    if (!$params->nameExist('getCodeAll')) {
      $params->select(['params.OKATO', 'params.OKTMO', 'params.KLADR'], ['params' => 'addr_obj_params'])
        ->where('params.objectid_addr', '=', $objectId)
        ->name('getCodeAll');
    }

    return $params->execute([$objectId], 'getCodeAll');
  }
}

