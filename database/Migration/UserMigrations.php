<?php declare(strict_types=1);

namespace DB\Migration;

use QueryBox\DBFacade;
use QueryBox\Migration\MetaTable;
use QueryBox\Migration\MigrateAble;

class UserMigrations
{
	static function migrateFromConfig(): void
	{
		//TODO: write custom exceptions
		$classList = $_SERVER['config']('migration');
		$migrateTool = MetaTable::createImmutable(DBFacade::getImmutableDBConnection());

		foreach ($classList as $params) {
			if (is_string($params) && is_a($params, MigrateAble::class, true)) {
				$migrateTool->doMigrateFromMigrateAble($params);
			}
		}
	}

	/**
	 * Delete tables from config
	 * @return void
	 */
	static function dropTablesFromConfig(): void
	{
		$classList = $_SERVER['config']('migration');
		$migrateTool = MetaTable::createImmutable(DBFacade::getImmutableDBConnection());

		$migrateTool->doDeleteTableFromMigrateAble($classList);
	}

}
