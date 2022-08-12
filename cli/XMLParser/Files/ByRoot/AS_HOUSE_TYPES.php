<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\Housetype;
use CLI\XMLParser\Files\XMLFile;

class AS_HOUSE_TYPES extends XMLFile
{
	/**
	 * @inheritDoc
	 */
	public static function getTable(): mixed
	{
		return new Housetype(['id', 'short', 'disc']);
	}

	/**
	 * @inheritDoc
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
	 */
    public function execDoWork(array &$values, mixed &$table): void
    {
        $table->forceInsert($values);
    }
}
