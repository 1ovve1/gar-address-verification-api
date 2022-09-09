<?php

declare(strict_types=1);

namespace GAR\Storage;

use DB\Exceptions\Unchecked\BadQueryResultException;
use DB\Exceptions\Unchecked\FailedDBConnectionWithDBException;
use GAR\Exceptions\Checked\CodeNotFoundException;
use GAR\Exceptions\Unchecked\ServerSideProblemException;

/**
 * Repository for getting code by concrete objectid
 */
class CodeByObjectIdStorage extends BaseStorage
{
	/**
	 * Return code by specific $type and objectid
	 * @param int $objectId - concrete objectid address
	 * @param string $type - type of code
	 * @return array<int, array<string, string>>
	 * @throws CodeNotFoundException - if codes was not found
	 */
    public function getCode(int $objectId, string $type): ?array
    {
		$code = [];

		if (Codes::tryFrom($type)) {
			if (Codes::from($type) === Codes::ALL) {
				$code = $this->getAllCodesByObjectId($objectId);
			} else {
				$code = [$this->getCodeByObjectId($objectId, $type)];
			}
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
	 * @return array<string, string>
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
	 * @return array<int, array<string, string>>
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