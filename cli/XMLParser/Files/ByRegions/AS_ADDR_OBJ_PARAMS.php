<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use CLI\XMLParser\Files\XMLFile;
use DB\Models\AddrObjParams;

class AS_ADDR_OBJ_PARAMS extends XMLFile
{
	/**
	 * {@inheritDoc}
	 */
	public static function getTable(): AddrObjParams
	{
		return new AddrObjParams();
	}

	/**
	 * @inheritDoc
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
	 */
    public function execDoWork(array &$values, mixed &$table): void
    {
        $region = $this->getIntRegion();

        if (in_array($values['TYPEID'], [6, 7, 10], true)) {
            if (!empty($table->executeTemplate('getFirstObjectIdAddrObj', [$region, $values['OBJECTID']]))) {
                $values['TYPEID'] = match ($values['TYPEID']) {
                    6 => 'OKATO',
                    7 => 'OKTMO',
                    10 => 'KLADR',
                };

                $values['REGION'] = $region;

                $table->forceInsert($values);
            }
        }
    }

}
