<?php declare(strict_types=1);

namespace CLI\Models;

use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;


class HousesAddtype extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'id'    => 'TINYINT UNSIGNED NOT NULL PRIMARY KEY',
	            'short' => 'CHAR(15)',
	            'disc'  => 'CHAR(50)',
			],
		];
	}

}
