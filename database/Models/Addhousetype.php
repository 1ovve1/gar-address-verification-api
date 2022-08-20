<?php declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;


class Addhousetype extends QueryBuilder implements MigrateAble
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
