<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\DBAdapter\InsertBuffer;
use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;


class AddrObj extends QueryBuilder implements MigrateAble
{

	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'checkIfAddrObjExists' =>
				AddrObj::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),

		];
	}

	function checkIfAddrObjNotExists(int $region, int $addrObjId): bool
	{
		if ($this->forceInsertTemplate instanceof InsertBuffer) {
			if ($this->forceInsertTemplate->checkValueInBufferExist($addrObjId, 'objectid')) {
				return false;
			}
		}
		return $this->userStates['checkIfAddrObjExists']
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
//	            'objectguid'    => 'CHAR(36) NOT NULL',
				'id_level'      => 'TINYINT UNSIGNED NOT NULL',
				'name'          => 'VARCHAR(255) NOT NULL',
				'id_typename'   => 'SMALLINT UNSIGNED NOT NULL',
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'id_level' => [AddrObjLevels::class, 'id'],
				'id_typename' => [AddrObjTypename::class, 'id']
			],
		];
	}


}
