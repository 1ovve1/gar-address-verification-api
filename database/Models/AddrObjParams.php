<?php

declare(strict_types=1);

namespace DB\Models;

use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;


class AddrObjParams extends QueryBuilder implements MigrateAble
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

	function checkIfAddrObjExists(int $region, int $addrObjId): bool
	{
		return $this->userStates['checkIfAddrObjExists']
			->execute([$region, $addrObjId])
			->isNotEmpty();
	}

	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'objectid_addr'     => 'INT UNSIGNED NOT NULL',
				'id_types'          => 'TINYINT UNSIGNED NOT NULL',
				'value'             => 'CHAR(31) NOT NULL',
				'region'            => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'objectid_addr'     => [AddrObj::class, 'objectid'],
				'id_types'          => [AddrObjParamsTypes::class, 'id']
			]
		];
	}
}
