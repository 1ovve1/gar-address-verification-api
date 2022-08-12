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

	    switch ($values['TYPEID']) {
		    case 6:
				$values['TYPEID'] = 'OKATO'; break;
		    case 7:
			    $values['TYPEID'] = 'OKTMO'; break;
		    case 10:
			    $values['TYPEID'] = 'KLADR'; break;
		    default:
				return;
	    };

        if ($table->getFirstObjectIdAddrObj($region, $values['OBJECTID'])) {
            $values['REGION'] = $region;

            $table->forceInsert($values);
        }
    }

}
