<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\Addhousetype;
use CLI\XMLParser\Files\XMLFile;
use DB\ORM\QueryBuilder\QueryBuilder;

class AS_ADDHOUSE_TYPES extends XMLFile
{
	/**
	 * @inheritDoc
	 * @return Addhousetype
	 */
	public static function getTable(): Addhousetype
	{
		return new Addhousetype();
	}

	/**
	 * @inheritDoc
	 * @param Addhousetype $table
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
	 * @param Addhousetype $table
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
