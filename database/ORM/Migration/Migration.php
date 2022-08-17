<?php declare(strict_types=1);

namespace DB\ORM\Migration;

use DB\ORM\DBAdapter\DBAdapter;

interface Migration
{
	/**
	 * Static migration
	 *
	 * @param DBAdapter $db
	 * @param string $tableName
	 * @param array<string, string|array<string, string>> $paramsToCreate
	 * @return bool
	 */
	static function migrate(DBAdapter $db,
	                        string $tableName,
	                        array $paramsToCreate = []): bool;

	/**
	 * Do migrate using db connection and class that implement MigrateAble interface
	 *
	 * @param DBAdapter $db
	 * @param string $className
	 * @return bool
	 */
	static function migrateFromMigrateAble(DBAdapter $db, string $className): bool;

	/**
	 * Create immutable migrate object using db connection
	 *
	 * @param DBAdapter $db
	 * @return self
	 */
	static function createImmutable(DBAdapter $db): self;

	/**
	 * Immutable migrate function
	 *
	 * @param string $tableName
	 * @param array<string, string|array<string, string>> $paramsToCreate
	 * @return bool
	 */
	function doMigrate(string $tableName,
	                   array $paramsToCreate = []): bool;

	/**
	 * Immutable migrate function using object that implement MigrateAble interface
	 *
	 * @param string $className
	 * @return bool
	 */
	function doMigrateFromMigrateAble(string $className): bool;
}