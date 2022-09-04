<?php declare(strict_types=1);

namespace DB;

use DB\Exceptions\BadQueryResultException;
use DB\Exceptions\FailedDBConnectionWithDBException;
use DB\ORM\DBFacade;
use DB\ORM\Migration\MetaTable;
use DB\ORM\Migration\MigrateAble;
use RuntimeException;

class UserMigrations
{
	/**
	 * @throws FailedDBConnectionWithDBException
	 * @throws BadQueryResultException
	 */
	static function migrateFromConfig(): void
	{
		//TODO: write custom exceptions
		$classList = $_SERVER['config']('migration');
		$migrateTool = MetaTable::createImmutable(DBFacade::getImmutableDBConnection());

		foreach ($classList as $tableName => $params) {
			if (is_string($params) && is_a($params, MigrateAble::class, true)) {
				try {
					$migrateTool->doMigrateFromMigrateAble($params);
				} catch (Exceptions\BadQueryResultException $e) {
					//TODO: write custom exception
					echo 'Migration failed' . PHP_EOL . $e->getMessage() . PHP_EOL;
					die();
				}
			} else if (is_array($params)) {
				if (is_string($tableName)) {
					if (key_exists('fields', $params)) {
						try {
							$migrateTool->doMigrate($tableName, $params);
						} catch (Exceptions\BadQueryResultException $e) {
							//TODO: write custom exception
							echo 'Migration failed' . PHP_EOL . $e->getMessage() . PHP_EOL;
							die();
						}
						continue;
					}
					var_dump($params);
					throw new RuntimeException("Params of migration should contains at least 'fields' param");
				}
				var_dump($params);
				throw new RuntimeException("Params of migration should contains string class name of MigrateAble implements or array definition");
			} else {
				var_dump($params);
				throw new RuntimeException("Invalid migrate configuration: {$tableName}");
			}

		}
	}

	/**
	 * Delete tables from config
	 * @return void
	 * @throws FailedDBConnectionWithDBException
	 * @throws BadQueryResultException
	 */
	static function dropTablesFromConfig(): void
	{
		$classList = $_SERVER['config']('migration');
		$migrateTool = MetaTable::createImmutable(DBFacade::getImmutableDBConnection());

		try {
			$migrateTool->doDeleteTableFromMigrateAble($classList);
		} catch (Exceptions\BadQueryResultException $e) {
			//TODO: write custom exception
			echo 'Drop tables failed' . PHP_EOL . $e->getMessage() . PHP_EOL;
			die();
		}
	}

}