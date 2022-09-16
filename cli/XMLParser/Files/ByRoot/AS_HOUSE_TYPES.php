<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\Housetype;
use CLI\XMLParser\Files\XMLFile;

class AS_HOUSE_TYPES extends XMLFile
{
	/**
	 * @inheritDoc
	 * @return Housetype
	 */
	public static function getTable(): Housetype
	{
		return new Housetype();
	}

	/**
	 * @inheritDoc
	 * @param Housetype $table
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
	 * @param Housetype $table
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
