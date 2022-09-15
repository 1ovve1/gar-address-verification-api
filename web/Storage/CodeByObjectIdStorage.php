<?php

declare(strict_types=1);

namespace GAR\Storage;

use GAR\Exceptions\Checked\CodeNotFoundException;
use GAR\Exceptions\Checked\ParamNotFoundException;

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
	 * @throws CodeNotFoundException|ParamNotFoundException - if codes was not found
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

			if (empty(current($code))) {
				throw new CodeNotFoundException($objectId);
			}
		} else {
			throw new CodeNotFoundException($objectId);
		}

        return $code;
    }

	/**
	 * Return code by $type using specific objectid address
	 * @param int $objectId - objectid address
	 * @param string $type - type of code
	 * @return array<string, string>
	 * @throws ParamNotFoundException
	 */
    public function getCodeByObjectId(int $objectId, string $type): array
    {
		$data = $this->db->findAddrObjParamByObjectIdAndType($objectId, $type);
		$result = [];

		if ($data->isNotEmpty()) {
			$fetchData = $data->fetchAllAssoc();

			foreach ($fetchData as $dataElem) {
				$code = $dataElem['value'] ??
					throw new ParamNotFoundException("value from addrObjParams was not found", "objectid = {$objectId}, type = {$type}");

				$result = [strtoupper($type) => $code];
			}

		}

		return $result;
    }

	/**
	 * Return all codes using concrete objectid address
	 * @param int $objectId - concrete objectid address
	 * @return array<int, array<string, string>>
	 * @throws ParamNotFoundException
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