<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use CLI\Models\AddrObjLevels;
use CLI\XMLParser\Files\XMLFile;

class AS_OBJECT_LEVELS extends XMLFile
{
	/**
	 * @inheritDoc
	 * @return AddrObjLevels
	 */
	public static function getTable(): AddrObjLevels
	{
		return new AddrObjLevels();
	}

	/**
	 * @inheritDoc
	 * @param AddrObjLevels $table
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
        return 'OBJECTLEVEL';
    }

	/**
	 * @inheritDoc
	 */
    public static function getAttributes(): array
    {
        return [
            'LEVEL' => 'int',
            'NAME' => 'string',
            'ISACTIVE' => 'bool',
        ];
    }

	/**
	 * @inheritDoc
	 * @param array{
	 *     LEVEL: int,
	 *     NAME: string,
	 *     ISACTIVE: string
	 * } $values
	 * @param AddrObjLevels $table
	 */
    public function execDoWork(array $values, mixed $table): void
    {
        $table->forceInsert([
			$values['LEVEL'],
	        $values['NAME']
        ]);
    }
}
