<?php declare(strict_types=1);

namespace DB\ORM\Migration;

interface RollbackImmutable
{
	/**
	 * Delete table by the $tableName
	 * @param string|array<string> $tableName
	 * @return void
	 */
	function doDeleteTable(string|array $tableName): void;

	/**
	 * Delete table by the MigrateAble $className
	 * @param string|array<string> $className
	 * @return void
	 */
	function doDeleteTableFromMigrateAble(string|array $className): void;
}