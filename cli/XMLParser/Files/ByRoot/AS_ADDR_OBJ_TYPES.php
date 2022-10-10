<?php declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRoot;

use CLI\XMLParser\Files\XMLFile;
use DB\Models\AddrObjTypename;

class AS_ADDR_OBJ_TYPES extends XMLFile
{
	/**
	 * @inheritDoc
	 */
	public static function getElement(): string
	{
		return 'ADDRESSOBJECTTYPE';
	}

	/**
	 * @inheritDoc
	 */
	public static function getAttributes(): array
	{
		return [
			'ID' => 'int',
			'LEVEL' => 'int',
			'NAME' => 'string',
			'SHORTNAME' => 'string',
			'DESC' => 'string',
		];
	}

	/**
	 * @inheritDoc
	 * @return AddrObjTypename
	 */
	public static function getTable(): AddrObjTypename
	{
		return new AddrObjTypename();
	}

	/**
	 * @inheritDoc
	 * @param AddrObjTypename $table
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
	 *     SHORTNAME: string,
	 *     DESC: string,
	 *     LEVEL: int
	 * } $values
	 * @param AddrObjTypename $table
	 */
	public function execDoWork(array $values, mixed $table): void
	{
		$table->forceInsert([
			$values['ID'],
			$values['NAME'],
			$values['SHORTNAME'],
			$values['DESC'],
			$values['LEVEL'],
		]);
	}

}
