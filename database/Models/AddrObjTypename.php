<?php declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;

class AddrObjTypename extends QueryBuilder implements MigrateAble
{
	/**
	 *
	 * @param string $typename
	 * @return int
	 */
	function getTypenameOrCreate(string $typename): int
	{
		$tryFindInTable = AddrObjTypename::select('id')
			->where('typename', $typename)
			->limit(1)
			->save();

		if ($tryFindInTable->isEmpty()) {
			AddrObjTypename::insert(['typename' => $typename])->save();

			$tryFindInTable = AddrObjTypename::select('id')
				->where('typename', $typename)
				->limit(1)
				->save();
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
				'id' => 'TINYINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
				'typename' => 'VARCHAR(31) NOT NULL'
			],
		];
	}
}