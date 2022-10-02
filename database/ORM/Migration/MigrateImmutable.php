<?php declare(strict_types=1);

namespace DB\ORM\Migration;

use RuntimeException;

interface MigrateImmutable
{
	/**
	 * Immutable migrate function
	 *
	 * @param string $tableName
	 * @param MigrationParams $paramsToCreate
	 * @return void
	 */
	function doMigrate(string $tableName,
	                   array $paramsToCreate): void;

	/**
	 * Immutable migrate function using object that implement MigrateAble interface
	 *
	 * @param string $className
	 * @return void
	 * @throws RuntimeException - if className not implement MigrateAble or not exists
	 */
	function doMigrateFromMigrateAble(string $className): void;

}