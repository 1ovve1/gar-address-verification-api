<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use CLI\XMLParser\Files\XMLFile;
use DB\Models\AddrObjParams;
use DB\Models\AddrObjParamsTypes;

class AS_ADDR_OBJ_PARAMS extends XMLFile
{
	/**
	 * {@inheritDoc}
	 * @return array{addrObjParams: AddrObjParams, addrObjParamsTypes: AddrObjParamsTypes}
	 */
	public static function getTable(): array
	{
		return [
			'addrObjParams' => new AddrObjParams(),
			'addrObjParamsTypes' => new AddrObjParamsTypes(),
		];
	}

	/**
	 * @inheritDoc
	 * @param array{addrObjParams: AddrObjParams, addrObjParamsTypes: AddrObjParamsTypes} $table
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{
		$table['addrObjParams']->saveForceInsert();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getElement(): string
    {
        return 'PARAM';
    }

	/**
	 * {@inheritDoc}
	 */
    public static function getAttributes(): array
    {
        return [
            'OBJECTID' => 'int',
            'TYPEID' => 'int',
            'VALUE' => 'string',
        ];
    }

	/**
	 * {@inheritDoc}
	 * @param array{
	 *     OBJECTID: int,
	 *     TYPEID: int,
	 *     VALUE: string
	 * } $values
	 * @param array{addrObjParams: AddrObjParams, addrObjParamsTypes: AddrObjParamsTypes} $table
	 */
    public function execDoWork(array $values, mixed $table): void
    {
		['addrObjParams' => $addrObjParams, 'addrObjParamsTypes' => $addrObjParamsTypes] = $table;
        $region = $this->getIntRegion();

		if ($addrObjParamsTypes->checkIfExists($values['TYPEID']) &&
			$addrObjParams->checkIfAddrObjExists($region, $values['OBJECTID'])) {

			$addrObjParams->forceInsert([
				$values['OBJECTID'],
				$values['TYPEID'],
				$values['VALUE'],
				$region
			]);
		}
    }

}
