<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use DB\Models\ObjLevels;
use CLI\XMLParser\Files\XMLFile;

class AS_OBJECT_LEVELS extends XMLFile
{
	/**
	 * @inheritDoc
	 */
	public static function getTable(): mixed
	{
		return new ObjLevels(['id', 'disc']);
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
     * {@inheritdoc}
     */
    public function execDoWork(array &$values, mixed &$table): void
    {
        unset($values['ISACTIVE']);

        $table->forceInsert($values);
    }
}
