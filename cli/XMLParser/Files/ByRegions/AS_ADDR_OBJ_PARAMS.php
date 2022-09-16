<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use CLI\XMLParser\Files\XMLFile;
use DB\Models\AddrObjParams;

class AS_ADDR_OBJ_PARAMS extends XMLFile
{
	/**
	 * {@inheritDoc}
	 * @return AddrObjParams
	 */
	public static function getTable(): AddrObjParams
	{
		return new AddrObjParams();
	}

	/**
	 * @inheritDoc
	 * @param AddrObjParams $table
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
	 * @param AddrObjParams $table
	 */
    public function execDoWork(array $values, mixed $table): void
    {
        $region = $this->getIntRegion();

	    $type = match ($values['TYPEID']) {
		    6 => 'OKATO',
		    7 => 'OKTMO',
		    10 => 'KLADR',
		    default => false
	    };

        if ($type && $table->checkIfAddrObjExists($region, $values['OBJECTID'])) {

            $table->forceInsert([
				$values['OBJECTID'],
	            $type,
	            $values['VALUE'],
	            $region
            ]);
        }
    }

}
