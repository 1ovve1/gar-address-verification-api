<?php declare(strict_types=1);

namespace DB\ORM\Migration;

use DB\ORM\DBAdapter\DBAdapter;
use RuntimeException;

interface MigrateImmutable
{
	/**
	 * Immutable migrate function
	 *
	 * @param string $tableName
	 * @param array<string, array<string, string>> $paramsToCreate
	 * @return void
	 */
	function doMigrate(string $tableName,
	                   array $paramsToCreate = []): void;

	/**
	 * Immutable migrate function using object that implement MigrateAble interface
	 *
	 * @param string $className
	 * @return void
	 * @throws RuntimeException - if className not implement MigrateAble or not exists
	 */
	function doMigrateFromMigrateAble(string $className): void;

}