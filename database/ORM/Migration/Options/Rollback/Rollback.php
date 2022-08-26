<?php declare(strict_types=1);

namespace DB\ORM\Migration\Options\Rollback;

use DB\ORM\DBAdapter\DBAdapter;

interface Rollback
{
	/**
	 * Delete many tables in one req
	 * @param DBAdapter $db
	 * @param string|array<string> ...$tableName
	 * @return void
	 */
	static function deleteTable(DBAdapter $db, string|array $tableName): void;

	/**
	 * Delete many tables from MigrateAble instance $className
	 * @param DBAdapter $db
	 * @param string|array<string> ...$className
	 * @return void
	 */
	static function deleteTableFromMigrateAble(DBAdapter $db, string|array $className): void;


}