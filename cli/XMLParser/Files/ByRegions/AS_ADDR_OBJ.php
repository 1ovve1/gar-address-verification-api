<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\AddrObj;
use CLI\XMLParser\Files\XMLFile;
use DB\Models\AddrObjTypename;

class AS_ADDR_OBJ extends XMLFile
{
	/**
	 * {@inheritDoc}
	 * @return array{addrObj: AddrObj, addrObjTypename: AddrObjTypename}
	 */
	public static function getTable(): array
	{
		return [
			'addrObj' => new AddrObj(),
			'addrObjTypename' => new AddrObjTypename()
		];
	}

	/**
	 * {@inheritDoc}
	 * @param array{addrObj: AddrObj, addrObjTypename: AddrObjTypename} $table
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{
		$table['addrObj']->saveForceInsert();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getElement(): string
    {
        return 'OBJECT';
    }

	/**
	 * {@inheritDoc}
	 */
    public static function getAttributes(): array
    {
        return [
            'ISACTUAL' => 'bool',
            'ISACTIVE' => 'bool',
//            'ID' => 'int',
            'OBJECTID' => 'int',
//            'OBJECTGUID' => 'string',
            'LEVEL' => 'int',
            'NAME' => 'string',
            'TYPENAME' => 'string',
        ];
    }

	/**
	 * {@inheritDoc}
	 * @param array{addrObj: AddrObj, addrObjTypename: AddrObjTypename} $table
	 * @param array{
	 *     ISACTUAL: bool, ISACTIVE: bool,
	 *     OBJECTID: int, LEVEL: int,
	 *     NAME: string, TYPENAME: string
	 * } $values
	 */
    public function execDoWork(array $values, mixed $table): void
    {
        $region = $this->getIntRegion();
		['addrObj' => $addrObj, 'addrObjTypename' => $addrObjTypename] = $table;

        if ($addrObj->checkIfAddrObjNotExists($region, $values['OBJECTID'])) {
			$typeNameId = $addrObjTypename->getTypenameOrCreate($values['TYPENAME'], $values['LEVEL']);

            $addrObj->forceInsert([
				$values['OBJECTID'],
	            $values['LEVEL'],
	            $values['NAME'],
	            $typeNameId,
	            $region
            ]);
        }
    }
}
