<?php declare(strict_types=1);

namespace GAR\Repository;


class AddressByCodeRepository extends BaseRepo
{
  const OKATO = 1;
  const OKTMO = 2;
  CONST KLADR = 3;

  public function getAddressByCode(int $code, int $type) : array
  {
    $fullAddress = [];

    $fullAddress = $this->getAllAddressesByCode($code, $type);
    return $fullAddress;
  }

  public function getAllAddressesByCode(int $code, int $type) {
    $params = $this->getDatabase();


    $params = $params->select(['addr.name', 'addr.typename', 'addr.id_level'], ['addr' => 'addr_obj'])
      ->innerJoin('addr_obj_params as param', ['param.objectid_addr' => 'addr.objectid']);
    if ($type == self::OKATO) {
      $params = $params->where('param.OKATO', '=', $code);
    } else if ($type == self::OKTMO) {
      $params = $params->where('param.OKTMO', '=', $code);
    } else if ($type == self::KLADR) {
      $params = $params->where('param.KLADR', '=', $code);
    }

    return $params->orderBy('addr.id_level')->save();
  }
}

