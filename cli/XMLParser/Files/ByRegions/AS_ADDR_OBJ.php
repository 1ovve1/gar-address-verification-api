<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\AddrObj;
use CLI\XMLParser\Files\XMLFile;

class AS_ADDR_OBJ extends XMLFile
{
	/**
	 * {@inheritDoc}
	 * @return AddrObj
	 */
	public static function getTable(): AddrObj
	{
		return new AddrObj();
	}

	/**
	 * {@inheritDoc}
	 * @param AddrObj $table
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{
		$table->saveForceInsert();
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
	 * @param AddrObj $table
	 * @param array{
	 *     ISACTUAL: bool, ISACTIVE: bool,
	 *     OBJECTID: int, LEVEL: int,
	 *     NAME: string, TYPENAME: string
	 * } $values
	 */
    public function execDoWork(array $values, mixed $table): void
    {
        $region = $this->getIntRegion();

        if ($table->checkIfAddrObjNotExists($region, $values['OBJECTID'])) {

            $table->forceInsert([
				$values['OBJECTID'],
	            $values['LEVEL'],
	            $values['NAME'],
	            $values['TYPENAME'],
	            $region
            ]);
        }
    }
}
