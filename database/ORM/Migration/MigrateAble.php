<?php declare(strict_types=1);

namespace DB\ORM\Migration;

interface MigrateAble
{
	/**
	 * @return array{fields: array<string, string>, foreign?: array<string, array<int, string>>}
	 */
	static function migrationParams(): array;
}