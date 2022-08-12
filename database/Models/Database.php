<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\QueryBuilder\QueryBuilder;

const LEVEL = 5;

class Database extends QueryBuilder
{
	public static function getInstance(): self
	{
		static $state = null;

		return $state ?? $state = new self();
	}

	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'getSingleNameByObjectId' =>
				Database::select(
					['addr' => ['name', 'typename', 'objectid']],
					['addr' => 'addr_obj']
				)->where(
					['addr' => 'objectid'], '?'
				),


			'getChiledNameByObjectIdAndName' =>
				Database::select(
					['chiled' => ['name', 'typename', 'objectid']],
					['mun' => 'mun_hierarchy']
				)->innerJoin(
					['chiled' => 'addr_obj'],
					['chiled' => 'objectid', 'mun' => 'chiledobjid_addr']
				)->where(
					['mun' => 'parentobjid_addr'], '?'
				)->andWhere(
					fn($builder) =>
					$builder->where(
						"CONCAT(chiled.name, ' ', chiled.typename)", 'LIKE', '?'
					)->orWhere(
						"CONCAT(chiled.typename, ' ', chiled.name)", 'LIKE', '?'
					)
				),

			'getParentNameByObjectId' =>
				Database::select(
					['parent' => ['name', 'typename', 'objectid']],
					['mun' => 'mun_hierarchy']
				)->innerJoin(
					['parent' => 'addr_obj'],
					['parent' => 'objectid', 'mun' => 'parentobjid_addr']
				)->where(
					['mun' => 'chiledobjid_addr'],
					'?'
				),

			'getHousesByObjectId' =>
				Database::select(
					"TRIM(' ' FROM " .
					"CONCAT(" .
					"COALESCE(ht.short, ''), ' ', COALESCE(chiled.housenum, ''), ' ', " .
					"COALESCE(addht1.short, ''), ' ', COALESCE(chiled.addnum1, ''), ' ', " .
					"COALESCE(addht2.short, ''), ' ', COALESCE(chiled.addnum2, '')" .
					")" .
					") as house",
					['mun' => 'mun_hierarchy']
				)->innerJoin(
					['chiled' => 'houses'],
					['chiled' => 'objectid', 'mun' => 'chiledobjid_houses']
				)->leftJoin(
					['ht' => 'housetype'],
					['ht' => 'id', 'chiled' => 'id_housetype']
				)->leftJoin(
					['addht1' => 'addhousetype'],
					['addht1' => 'id', 'chiled' => 'id_addtype1']
				)->leftJoin(
					['addht2' => 'addhousetype'],
					['addht2' => 'id', 'chiled' => 'id_addtype2']
				)->where(
					['mun' => 'parentobjid_addr'],
					'?'
				),

			'getAddressObjectIdByName' =>
				Database::select(
					'DISTINCT(parent.objectid)',
					['mun' => 'mun_hierarchy']
				)->innerJoin(
					['parent' => 'addr_obj'],
					['parent' => 'objectid', 'mun' => 'parentobjid_addr']
				)->leftJoin(
					['chiled' => 'addr_obj'],
					['chiled' => 'objectid', 'mun' => 'chiledobjid_addr']
				)->where(
					['parent' => 'id_level'],
					'<=',
					'?'
				)->andWhere(
					fn($builder) =>
					$builder->where(
						fn($builder) =>
						$builder->where(
							"CONCAT(parent.name, ' ', parent.typename)",
							'LIKE',
							'?'
						)->orWhere(
							"CONCAT(parent.typename, ' ',parent.name)",
							'LIKE',
							'?'
						)
					)->andWhere(
						fn($builder) =>
						$builder->where(
							"CONCAT(chiled.name, ' ', chiled.typename)",
							'LIKE',
							'?'
						)->orWhere(
							"CONCAT(chiled.typename, ' ', chiled.name)",
							'LIKE',
							'?'
						)
					)
				)->limit(2),

			'getLikeAddress' =>
				Database::select(
					['addr' => ['name', 'typename', 'objectid']],
					['addr' => 'addr_obj']
				)->where(
					['addr' => 'id_level'],
					'<=',
					'?'
				)->andWhere(
					fn($builder) =>
					$builder->where(
						"CONCAT(addr.name, ' ', addr.typename)",
						'LIKE',
						'?'
					)->orWhere(
						"CONCAT(addr.typename, ' ', addr.name)",
						'LIKE',
						'?'
					)
				)->limit(100)
		];
	}

	/**
	 * Return singlename address name by objectud param of concrete address
	 * @param  int    $objectId - object id concrete address
	 * @return array<mixed>
	 */
	public function getSingleNameByObjectId(int $objectId): array
	{
		return $this->userStates['getSingleNameByObjectId']
			->execute([$objectId]);
	}

	/**
	 * Return chiled name of using parent objectid and chiled name fragment
	 * @param  int    $parentObjectId - parent address objectid
	 * @param  string $chiledName - chiled name fragment
	 * @return array<mixed>
	 */
	public function getChiledNameByObjectIdAndName(int $parentObjectId, string $chiledName): array
	{
		$chiledName .= '%';
		
		return $this->userStates['getChiledNameByObjectIdAndName']
			->execute([$parentObjectId, $chiledName, $chiledName]);
	}

	/**
	 * Return parent name using chiled address objectid
	 * @param int $chiledObjectId - chiled address objectid
	 * @return array<mixed>
	 */
	public function getParentNameByObjectId(int $chiledObjectId): array
	{
		return $this->userStates['getParentNameByObjectId']
			->execute([$chiledObjectId]);
	}

	/**
	 * Return houses object id using parent address objectid
	 * @param  int    $objectId - parent address objectid
	 * @return array<mixed>
	 */
	public function getHousesByObjectId(int $objectId): array
	{
		return $this->userStates['getHousesByObjectId']
			->execute([$objectId]);
	}

	/**
	 * Return parent address object id by parent and chiled address name
	 * @param  string $parentName - parent address name
	 * @param  string $chiledName - chiled address name
	 * @return array<mixed>
	 */
	public function getAddressObjectIdByName(string $parentName, string $chiledName): array
	{
		$parentName .= '%';
		$chiledName .= '%';

		return $this->userStates['getAddressObjectIdByName']
			->execute([LEVEL, $parentName, $parentName, $chiledName, $chiledName]);
	}

	/**
	 * Return like address name by address name fragment
	 * @param  string $halfAddress - address name fragment
	 * @return array<mixed>
	 */
	public function getLikeAddress(string $halfAddress): array
	{
		$halfAddress .= '%';

		return $this->userStates['getLikeAddress']->execute([LEVEL, $halfAddress, $halfAddress]);
	}
}
