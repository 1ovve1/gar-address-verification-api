<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\Exceptions\BadQueryResultException;
use DB\Exceptions\FailedDBConnectionWithDBException;
use GAR\Exceptions\CodeNotFoundException;
use GAR\Exceptions\ServerSideProblemException;

/**
 * Repository for getting code by concrete objectid
 */
class CodeByObjectIdRepository extends BaseRepo
{
	/**
	 * Return code by specific $type and objectid
	 * @param int $objectId - concrete objectid address
	 * @param string $type - type of code
	 * @return array|null
	 * @throws CodeNotFoundException - if codes was not found
	 * @throws FailedDBConnectionWithDBException
	 * @throws ServerSideProblemException - if we fined server side problems
	 */
    public function getCode(int $objectId, string $type): ?array
    {
		$code = [];

		try {
			if (Codes::tryFrom($type)) {
				if (Codes::from($type) === Codes::ALL) {
					$code = $this->getAllCodesByObjectId($objectId);
				} else {
					$code = $this->getCodeByObjectId($objectId, $type);
				}
			}
		} catch (BadQueryResultException $e) {
			throw new ServerSideProblemException($e);
		}

		if (empty($code)) {
			throw new CodeNotFoundException($objectId);
		}

        return $code;
    }

	/**
	 * Return code by $type using specific objectid address
	 * @param int $objectId - objectid address
	 * @param string $type - type of code
	 * @return array<mixed>
	 * @throws BadQueryResultException
	 * @throws FailedDBConnectionWithDBException
	 */
    public function getCodeByObjectId(int $objectId, string $type): array
    {
		$data = $this->db->findAddrObjParamByObjectIdAndType($objectId, $type);

		if ($data->isNotEmpty()) {
			$data = [strtoupper($type) => current($data->fetchAllAssoc())['value']];
		} else {
			$data = [];
		}

		return $data;
    }

	/**
	 * Return all codes using concrete objectid address
	 * @param int $objectId - concrete objectid address
	 * @return array<mixed>
	 * @throws BadQueryResultException
	 * @throws FailedDBConnectionWithDBException
	 */
    public function getAllCodesByObjectId(int $objectId): array
    {
        $types = [
            Codes::OKATO->value,
            Codes::OKTMO->value,
            Codes::KLADR->value,
        ];

		$resultData = [];
		foreach ($types as $type) {
			$data = $this->getCodeByObjectId($objectId, $type);

			if (empty($data)) {
				continue;
			}
			$resultData[] = $data;
		}

        return $resultData;
    }
}