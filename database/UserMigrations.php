<?php declare(strict_types=1);

namespace DB;

use DB\ORM\DBFacade;
use DB\ORM\Migration\MetaTable;
use DB\ORM\Migration\MigrateAble;

class UserMigrations
{
	static function doMigrateFromConfig(): void
	{
		$classList = $_SERVER['config']('migration');
		$migrateTool = MetaTable::createImmutable(DBFacade::getImmutableDBConnection());

		foreach ($classList as $tableName => $params) {
			if (is_string($params) && is_a($params, MigrateAble::class, true)) {
				$migrateTool->doMigrateFromMigrateAble($params);
			} else if (is_array($params)) {
				if (is_string($tableName)) {
					if (in_array('fields', $params)) {
						$migrateTool->doMigrate($tableName, $params);
						continue;
					}
					var_dump($params);
					throw new \RuntimeException("Params of migration should contains at least 'fields' param");
				}
				var_dump($params);
				throw new \RuntimeException("Params of migration should contains string class name of MigrateAble implements or array definition");
			} else {
				var_dump($params);
				throw new \RuntimeException("Invalid migrate configuration: {$tableName}");
			}

		}
	}
}