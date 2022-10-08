<?php declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\Migration\MigrationParams;
use DB\ORM\QueryBuilder\QueryBuilder;

class AddrObjParamsTypes extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'checkIfExists' => AddrObjParamsTypes::select('id')
				->where('id')
				->limit(1),
		];
	}

	function checkIfExists(int $id): bool
	{
		return $this->userStates['checkIfExists']->execute([$id])->isNotEmpty();
	}

	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'id' => 'TINYINT UNSIGNED NOT NULL PRIMARY KEY',
				'code' => 'VARCHAR(30) NOT NULL',
				'name' => 'VARCHAR(40) NOT NULL',
				'desc' => 'VARCHAR(150) NOT NULL',
			],
		];
	}

}