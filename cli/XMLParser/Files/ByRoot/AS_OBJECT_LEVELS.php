<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\ObjLevels;
use CLI\XMLParser\Files\XMLFile;

class AS_OBJECT_LEVELS extends XMLFile
{
	/**
	 * @inheritDoc
	 * @return ObjLevels
	 */
	public static function getTable(): ObjLevels
	{
		return new ObjLevels();
	}

	/**
	 * @inheritDoc
	 * @param ObjLevels $table
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
	 * @param ObjLevels $table
	 */
    public function execDoWork(array $values, mixed $table): void
    {
        $table->forceInsert([
			$values['LEVEL'],
	        $values['NAME']
        ]);
    }
}
