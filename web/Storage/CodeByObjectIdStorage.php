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
	 * @param Codes $type - type of code
	 * @param int $region
	 * @return array<string, mixed>
	 * @throws CodeNotFoundException|ParamNotFoundException - if codes was not found
	 */
    public function getCode(int $objectId, Codes $type, int $region): ?array
    {
		$this->setRegionContext($region);

		if ($type === Codes::ALL) {
			$code = $this->getAllCodesByObjectId($objectId);
		} else {
			$code = $this->getCodeByObjectId($objectId, $type);
		}

		if (empty(current($code))) {
			throw new CodeNotFoundException($objectId);
		}

        return $code;
    }

	/**
	 * Return code by $type using specific objectid address
	 * @param int $objectId - objectid address
	 * @param Codes $type - type of code
	 * @return array<string, mixed>
	 * @throws ParamNotFoundException
	 */
    public function getCodeByObjectId(int $objectId, Codes $type): array
    {
		$data = $this->db->findAddrObjParamByObjectIdAndType($objectId, $type->value, $this->getRegionContext());
		$result = [];

	    if ($data->isNotEmpty()) {
			$fetchData = $data->fetchAllAssoc();

			foreach ($fetchData as $dataElem) {
				$code = $dataElem['value'] ??
					throw new ParamNotFoundException(
						"value from addrObjParams was not found",
						"objectid = {$objectId}, type = {$type->value}"
					);

				$result = [$type->value => $code];
			}

		}

		return $result;
    }

	/**
	 * Return all codes using concrete objectid address
	 * @param int $objectId - concrete objectid address
	 * @return array<string, mixed>
	 */
    public function getAllCodesByObjectId(int $objectId): array
    {
		$resultData = [];
		$rawData = $this->db->findAllAddrObjParamByObjectId($objectId, $this->getRegionContext());

	    /**
	     * @var mixed $value
	     * @var string $code
	     */
		foreach ($rawData->fetchAllAssoc() as ['value' => $value, 'code' => $code]) {
			$resultData[$code] = $value;
		}

        return $resultData;
    }
}