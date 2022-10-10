<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\HousesAddtype;
use CLI\XMLParser\Files\XMLFile;

class AS_ADDHOUSE_TYPES extends XMLFile
{
	/**
	 * @inheritDoc
	 * @return HousesAddtype
	 */
	public static function getTable(): HousesAddtype
	{
		return new HousesAddtype();
	}

	/**
	 * @inheritDoc
	 * @param HousesAddtype $table
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{
		$table->saveForceInsert();
	}

	/**
	 * @inheritDoc
	 */
    public static function getElement(): string
    {
        return 'HOUSETYPE';
    }

	/**
	 * @inheritDoc
	 */
    public static function getAttributes(): array
    {
        return [
            'ID' => 'int',
            'SHORTNAME' => 'string',
            'NAME' => 'string',
        ];
    }

	/**
	 * @inheritDoc
	 * @param array{
	 *     ID: int,
	 *     SHORTNAME: string,
	 *     NAME: string
	 * } $values
	 * @param HousesAddtype $table
	 */
    public function execDoWork(array $values, mixed $table): void
    {
        $table->forceInsert([
			$values['ID'],
	        $values['SHORTNAME'],
	        $values['NAME']
        ]);
    }
}
