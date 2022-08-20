<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;


class Houses extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'getFirstObjectId' =>
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
//				'id'            => 'INT UNSIGNED NOT NULL',
				'objectid'      => 'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
//				'objectguid'    => 'VARCHAR(36) NOT NULL',
				'housenum'      => 'VARCHAR(50)',
				'addnum1'       => 'VARCHAR(50)',
				'addnum2'       => 'VARCHAR(50)',
				'id_housetype'  => 'TINYINT UNSIGNED',
				'id_addtype1'   => 'TINYINT UNSIGNED',
				'id_addtype2'   => 'TINYINT UNSIGNED',
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'id_housetype'  => [Housetype::class, 'id'],
				'id_addtype1'   => [Addhousetype::class, 'id'],
				'id_addtype2'   => [Addhousetype::class, 'id'],
			]
		];
	}
}
