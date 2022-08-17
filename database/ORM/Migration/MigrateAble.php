<?php declare(strict_types=1);

namespace DB\ORM\Migration;

interface MigrateAble
{
	/**
	 * @return array<string, array<string, string>>
	 */
	static function migrationParams(): array;
}