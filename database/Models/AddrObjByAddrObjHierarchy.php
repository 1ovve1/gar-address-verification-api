<?php declare(strict_types=1);

namespace DB\Models;

use DB\ORM\DBAdapter\InsertBuffer;
use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;

class AddrObjByAddrObjHierarchy extends QueryBuilder implements MigrateAble
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'checkIfAddrObjExist' =>
				AddrObj::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),
			'checkIfMapNotExist' =>
				AddrObjByAddrObjHierarchy::select('region')
					->where('region')
					->andWhere(fn($builder) => $builder
						->where('parentobjid_addr')
						->orWhere('chiledobjid_addr')
					)->limit(1),
			'checkIfChiledNotExist' =>
				AddrObjByAddrObjHierarchy::select('region')
					->where('region')
					->andWhere('chiledobjid_addr')
					->limit(1),
		];
	}

	function checkIfAddrObjExist(int $region, int $addrObjId): bool
	{
		return $this->userStates['checkIfAddrObjExist']
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
			if ($this->forceInsertTemplate->checkValueInBufferExist($chiled, 'chiledobjid_addr')) {
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
				'parentobjid_addr' => "BIGINT UNSIGNED NOT NULL",
				'chiledobjid_addr' => "BIGINT UNSIGNED NOT NULL PRIMARY KEY",
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'parentobjid_addr'      => [AddrObj::class, 'objectid'],
				'chiledobjid_addr'      => [AddrObj::class, 'objectid'],
			]
		];
	}

}