<?php declare(strict_types=1);

namespace DB\ORM\Migration;

interface MigrateAble
{
	/**
	 * @return MigrationParams
	 */
	static function migrationParams(): array;
}