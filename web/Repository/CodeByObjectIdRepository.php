<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\Exceptions\BadQueryResultException;
use DB\Models\AddrObjParams;
use DB\Models\Database;

/**
 * Repository for getting code by concrete objectid
 */
class CodeByObjectIdRepository extends BaseRepo
{
    /**
     * Return code by specific $type and objectid
     * @param  int    $objectId - concrete objectid address
     * @param  string $type - type of code
     * @return array<mixed>
     */
    public function getCode(int $objectId, string $type): ?array
    {
        $code = null;

        if (Codes::tryFrom($type)) {
            if (Codes::from($type) === Codes::ALL) {
                $code = $this->getAllCodesByObjectId($objectId);
            } else {
                $code = $this->getCodeByObjectId($objectId, $type);
            }
        } else {
            throw new \RuntimeException('type of code not found');
        }
        return $code;
    }

	/**
	 * Return code by $type using specific objecid address
	 * @param int $objectId - objectid address
	 * @param string $type - type of code
	 * @return array<mixed>
	 * @throws BadQueryResultException
	 */
    public function getCodeByObjectId(int $objectId, string $type): array
    {
		$data = Database::select(
			['params' => 'value'],
			['params' => AddrObjParams::table()]
		)->where(
			['params' => 'objectid_addr'],
			$objectId
		)->andWhere(
			['params' => 'type'],
			$type
		)->limit(1)->save()->fetchAllAssoc();

		if (!empty($data)) {
			$data = [strtoupper($type) => $data[0]['value']];
		}

		return $data;
    }

	/**
	 * Return all codes using concrete objectid address
	 * @param int $objectId - concrete objectid address
	 * @return array<mixed>
	 * @throws BadQueryResultException
	 */
    public function getAllCodesByObjectId(int $objectId): array
    {
        $types = [
            Codes::OKATO->value,
            Codes::OKTMO->value,
            Codes::KLADR->value,
        ];


        $queryResult = Database::select(
	        ['params' => ['value', 'type']],
	        ['params' => AddrObjParams::table()],
        )->where(
	        ['params' => 'objectid_addr'],
	        $objectId
        )->save()->fetchAllAssoc();

        if (empty($queryResult)) {
            return [];
        }

	    $response = [];
	    foreach ($types as $type) {
            $type = strtoupper($type);

            foreach ($queryResult as $data) {
                if ($data['type'] === $type) {
                    $response[$type] = $data['value'];
                    break;
                }
            }
            if (!array_key_exists($type, $response)) {
                $response[$type] = null;
            }
        }

        return $response;
    }
}
