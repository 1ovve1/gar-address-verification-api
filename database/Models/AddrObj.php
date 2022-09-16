<?php

declare(strict_types=1);

namespace DB\Models;

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
				'objectid'      => 'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
//	            'objectguid'    => 'CHAR(36) NOT NULL',
				'id_level'      => 'TINYINT UNSIGNED NOT NULL',
				'name'          => 'VARCHAR(255) NOT NULL',
				'typename'      => 'VARCHAR(31) NOT NULL',
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'id_level' => [ObjLevels::class, 'id'],
			],
		];
	}


}
