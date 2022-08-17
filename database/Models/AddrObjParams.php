<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;


class AddrObjParams extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'getFirstObjectIdAddrObj' =>
				AddrObj::select('objectid')
				->where('region')
				->andWhere('objectid')
				->limit(1),
		];
	}

	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'objectid_addr'     => 'BIGINT UNSIGNED NOT NULL',
				'type'              => 'CHAR(5) NOT NULL',
				'value'             => 'CHAR(31) NOT NULL',
				'region'            => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'objectid_addr'     => [AddrObj::class, 'objectid']
			]
		];
	}
}
