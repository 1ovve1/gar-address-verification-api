<?php declare(strict_types=1);

namespace GAR\Repository;


use GAR\Database\Table\SQL\QueryModel;
use GAR\Repository\Codes;

/**
 * Repository for getting code by concrete objectid
 */
class AddressByCodeRepository extends BaseRepo
{

  /**
   * Return code by specific $type and objectid
   * @param  int    $objectId - concrete objectid address
   * @param  string $type - type of code
   * @return array<mixed>
   */
  public function getCode(int $objectId, string $type) : ?array
  {
    $code = null;

    if (Codes::tryFrom($type)) {
      if (Codes::from($type) === Codes::ALL) {
        $code = $this->getAllCodesByObjectId($objectId);
      } else {
        $code = $this->getCodeByObjectId($objectId, $type);
      }
      if (!empty($code)) {
        $code = $code[0];
      }
    }
    return $code;
  }

  /**
   * Return code by $type using specific objecid address
   * @param  int    $objectId - objectid address
   * @param  string $type - type of code
   * @return array<mixed>
   */
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

  /**
   * Return all codes using concrete objectid address
   * @param  int    $objectId - cocrete objectid address
   * @return array<mixed>
   */
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

