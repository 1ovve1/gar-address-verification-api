<?php declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use CLI\XMLParser\Files\XMLFile;
use DB\Models\AddrObjParamsTypes;

class AS_PARAM_TYPES extends XMLFile
{
	/**
	 * @inheritDoc
	 */
	public static function getElement(): string
	{
		return 'PARAMTYPE';
	}

	/**
	 * @inheritDoc
	 */
	public static function getAttributes(): array
	{
		return [
			'ID' => 'int',
			'NAME' => 'string',
			'DESC' => 'string',
			'CODE' => 'string'
		];
	}

	/**
	 * @inheritDoc
	 * @return AddrObjParamsTypes
	 */
	public static function getTable(): AddrObjParamsTypes
	{
		return new AddrObjParamsTypes();
	}

	/**
	 * @inheritDoc
	 * @param AddrObjParamsTypes $table
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{
		$table->saveForceInsert();
	}

	/**
	 * @inheritDoc
	 * @param array{
	 *     ID: int,
	 *     NAME: string,
	 *     DESC: string,
	 *     CODE: string
	 * } $values
	 * @param AddrObjParamsTypes $table
	 */
	public function execDoWork(array $values, mixed $table): void
	{
		$table->forceInsert([
			$values['ID'],
			$values['CODE'],
			$values['NAME'],
			$values['DESC'],
		]);
	}


}
