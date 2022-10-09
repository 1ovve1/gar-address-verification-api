<?php

declare(strict_types=1);

namespace GAR\Models;

use QueryBox\DBAdapter\InsertBuffer;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;


class Houses extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'checkIfHousesObjExists' =>
				Houses::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),
		];
	}

	function checkIfHousesObjNotExists(int $region, int $addrObjId): bool
	{
		if ($this->forceInsertTemplate instanceof InsertBuffer) {
			if ($this->forceInsertTemplate->checkValueInBufferExist($addrObjId, 'objectid')) {
				return false;
			}
		}
		return $this->userStates['checkIfHousesObjExists']
			->execute([$region, $addrObjId])
			->isEmpty();
	}

	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
//				'id'            => 'INT UNSIGNED NOT NULL',
				'objectid'      => 'INT UNSIGNED NOT NULL PRIMARY KEY',
//				'objectguid'    => 'VARCHAR(36) NOT NULL',
				'housenum'      => 'VARCHAR(50)',
				'addnum1'       => 'VARCHAR(50)',
				'addnum2'       => 'VARCHAR(50)',
				'id_type'       => 'TINYINT UNSIGNED',
				'id_addtype1'   => 'TINYINT UNSIGNED',
				'id_addtype2'   => 'TINYINT UNSIGNED',
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'id_type'       => [HousesType::class, 'id'],
				'id_addtype1'   => [HousesAddtype::class, 'id'],
				'id_addtype2'   => [HousesAddtype::class, 'id'],
			]
		];
	}
}
