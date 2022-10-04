<?php declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;

class AddrObjTypename extends QueryBuilder implements MigrateAble
{
	/**
	 *
	 * @param string $typename
	 * @param int $level
	 * @return int
	 */
	function getTypenameOrCreate(string $typename, int $level): int
	{
		$state = AddrObjTypename::select('id')
			->where('short', $typename)
			->andWhere('id_level', $level)
			->limit(1);

		$tryFindInTable = $state->save();

		if ($tryFindInTable->isEmpty()) {
			AddrObjTypename::insert(
				['name' => $typename, 'short' => $typename, 'desc' => $typename, 'id_level' => $level]
			)->save();

			$tryFindInTable = $state->save();
		}

		/** @var int $index */
		[[$index]] = $tryFindInTable->fetchAllNum();

		return $index;
	}

	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'id' => 'SMALLINT UNSIGNED NOT NULL PRIMARY KEY',
				'name' => 'VARCHAR(100) NOT NULL',
				'short' => 'VARCHAR(31) NOT NULL',
				'desc' => 'VARCHAR(110) NOT NULL',
				'id_level' => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'id_level' => [AddrObjLevels::class, 'id'],
			],
		];
	}
}