<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;


class MunHierarchy extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'getIdAddrObj' =>
				AddrObj::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),
			'getIdHouses' =>
				Houses::select('region')
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
				'parentobjid_addr'      => 'INT UNSIGNED NOT NULL',
				'chiledobjid_addr'      => 'INT UNSIGNED',
				'chiledobjid_houses'    => 'BIGINT UNSIGNED',
				'region'                => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'parentobjid_addr'      => [AddrObj::class, 'objectid'],
				'chiledobjid_addr'      => [AddrObj::class, 'objectid'],
				'chiledobjid_houses'    => [Houses::class, 'objectid']
			]
		];
	}
}
