<?php declare(strict_types=1);

namespace CLI\Models;

use QueryBox\DBAdapter\InsertBuffer;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;

class HousesByAddrObjHierarchy extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'checkIfHousesObjExists' =>
				Houses::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),
			'checkIfMapNotExist' =>
				HousesByAddrObjHierarchy::select('region')
					->where('region')
					->andWhere(fn($builder) => $builder
							->where('parentobjid_addr')
							->andWhere('chiledobjid_houses')
					)->limit(1),
			'checkIfChiledNotExist' =>
				HousesByAddrObjHierarchy::select('region')
					->where('region')
					->andWhere('chiledobjid_houses')
					->limit(1),
		];
	}

	function checkIfHousesObjExists(int $region, int $addrObjId): bool
	{
		return $this->userStates['checkIfHousesObjExists']
			->execute([$region, $addrObjId])
			->isNotEmpty();
	}

	function checkIfMapNotExist(int $region, int $parent, int $chiled): bool
	{
		if ($this->userStates['checkIfMapNotExist']->execute([$region, $parent, $chiled])->isNotEmpty()) {
			return false;
		}
		if ($this->forceInsertTemplate instanceof InsertBuffer) {
			if ($this->forceInsertTemplate->checkIfRecordInBufferExist([$parent, $chiled, $region])) {
				return false;
			}
		}

		return true;
	}

	function checkIfChiledNotExist(int $region, int $chiled): bool
	{
		if ($this->userStates['checkIfChiledNotExist']->execute([$region, $chiled])->isNotEmpty()) {
			return false;
		}
		if ($this->forceInsertTemplate instanceof InsertBuffer) {
			if ($this->forceInsertTemplate->checkValueInBufferExist($chiled, 'chiledobjid_houses')) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'parentobjid_addr'      => "INT UNSIGNED NOT NULL",
				'chiledobjid_houses'    => "INT UNSIGNED NOT NULL PRIMARY KEY",
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'parentobjid_addr'      => [AddrObj::class, 'objectid'],
				'chiledobjid_houses'    => [Houses::class, 'objectid'],
			]
		];
	}
}