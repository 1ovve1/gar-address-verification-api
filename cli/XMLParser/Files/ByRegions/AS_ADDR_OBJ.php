<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\AddrObj;
use CLI\XMLParser\Files\XMLFile;
use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryBuilder;

class AS_ADDR_OBJ extends XMLFile
{
	/**
	 * {@inheritDoc}
	 */
	public static function getTable(): AddrObj
	{
		return new AddrObj();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function callbackOperationWithTable(QueryBuilder $table): void
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
	 */
    public function execDoWork(array &$values, mixed &$table): void
    {
        $region = $this->getIntRegion();

        if ($table->getFirstObjectId($region, $values['OBJECTID'])->isEmpty()) {

            unset($values['ISACTUAL']); unset($values['ISACTIVE']);

            $values['REGION'] = $region;

            $table->forceInsert($values);
        }
    }
}
