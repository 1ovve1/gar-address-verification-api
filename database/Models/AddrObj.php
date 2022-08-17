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
			'getFirstObjectId' =>
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
