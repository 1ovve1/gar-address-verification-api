<?php declare(strict_types=1);

namespace DB\Models;

use DB\ORM\Migration\MigrateAble;
use DB\ORM\QueryBuilder\QueryBuilder;

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
		return $this->userStates['checkIfMapNotExist']
			->execute([$region, $parent, $chiled])
			->isEmpty();
	}

	function checkIfChiledNotExist(int $region, int $chiled): bool
	{
		return $this->userStates['checkIfChiledNotExist']
			->execute([$region, $chiled])
			->isEmpty();
	}

	/**
	 * @inheritDoc
	 */
	static function migrationParams(): array
	{
		return [
			'fields' => [
				'parentobjid_addr' => "BIGINT UNSIGNED NOT NULL",
				'chiledobjid_houses' => "BIGINT UNSIGNED NOT NULL",
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'parentobjid_addr'      => [AddrObj::class, 'objectid'],
				'chiledobjid_houses'    => [Houses::class, 'objectid'],
			]
		];
	}
}