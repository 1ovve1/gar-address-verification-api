<?php

declare(strict_types=1);

namespace DB\Models;

use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;


class HousesType extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'id'    => 'TINYINT UNSIGNED NOT NULL PRIMARY KEY',
				'short' => 'CHAR(15) NOT NULL',
				'disc'  => 'CHAR(50) NOT NULL',
			],
		];
	}
}
