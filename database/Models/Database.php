<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\QueryBuilder\QueryBuilder;

const LEVEL = 5;

class Database extends QueryBuilder
{
	/**
	 * Get singleton instance
	 * @return static
	 */
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
			'getAddressByObjectId' =>
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
				)->andWhere(fn($builder) =>
					$builder->where(
						"CONCAT(chiled.name, ' ', chiled.typename)", 'LIKE', '?'
					)->orWhere(
						"CONCAT(chiled.typename, ' ', chiled.name)", 'LIKE', '?'
					)
				),

			'getParentAddressByObjectId' =>
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

			'findChainByParentAndChiledAddressName' =>
				Database::select(
					'DISTINCT mun.parentobjid_addr, mun.chiledobjid_addr',
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
					$builder->where(fn($builder) =>
						$builder->where(
							"CONCAT(parent.name, ' ', parent.typename)",
							'LIKE',
							'?'
						)->orWhere(
							"CONCAT(parent.typename, ' ',parent.name)",
							'LIKE',
							'?'
						)
					)->andWhere(fn($builder) =>
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
				)->andWhere(fn($builder) =>
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
	 * Return single name address name by objectid param of concrete address
	 * @param int $objectId - object id concrete address
	 * @return QueryResult
	 */
	public function getAddressByObjectId(int $objectId): QueryResult
	{
		return Database::select(
			['addr' => ['name', 'typename', 'objectid']],
			['addr' => AddrObj::table()]
		)->where(
			['addr' => 'objectid'], $objectId
		)->save();

//		 for using cache system
//		return $this->userStates['getAddressByObjectId']
//			->execute([$objectId]);
	}

	/**
	 * Return chiled name of using parent objectid and chiled name fragment
	 * @param int $parentObjectId - parent address objectid
	 * @param string $chiledName - chiled name fragment
	 * @return QueryResult
	 */
	public function getChiledAddressByParentObjectIdAndChiledName(int $parentObjectId, string $chiledName): QueryResult
	{
		$chiledName .= '%';

		return Database::select(
			['chiled' => ['name', 'typename', 'objectid']],
			['mun' => MunHierarchy::table()]
		)->innerJoin(
			['chiled' => AddrObj::table()],
			['chiled' => 'objectid', 'mun' => 'chiledobjid_addr']
		)->where(
			['mun' => 'parentobjid_addr'], $parentObjectId
		)->andWhere(fn($builder) =>
			$builder->where(
				"CONCAT(chiled.name, ' ', chiled.typename)", 'LIKE', $chiledName
			)->orWhere(
				"CONCAT(chiled.typename, ' ', chiled.name)", 'LIKE', $chiledName
			)
		)->save();

//		return $this->userStates['getChiledNameByObjectIdAndName']
//			->execute([$parentObjectId, $chiledName, $chiledName]);
	}

	/**
	 * Return parent name using chiled address objectid
	 * @param int $chiledObjectId - chiled address objectid
	 * @return QueryResult
	 */
	public function getParentAddressByObjectId(int $chiledObjectId): QueryResult
	{
		return Database::select(
			['parent' => ['name', 'typename', 'objectid']],
			['mun' => MunHierarchy::table()]
		)->innerJoin(
			['parent' => AddrObj::table()],
			['parent' => 'objectid', 'mun' => 'parentobjid_addr']
		)->where(
			['mun' => 'chiledobjid_addr'],
			$chiledObjectId
		)->save();

//		return $this->userStates['getParentAddressByObjectId']
//			->execute([$chiledObjectId]);
	}

	/**
	 * Return houses object id using parent address objectid
	 * @param int $objectId - parent address objectid
	 * @return QueryResult
	 */
	public function getHousesByParentObjectId(int $objectId): QueryResult
	{
		return Database::select(
			"TRIM(' ' FROM CONCAT(" .
				"COALESCE(ht.short, ''), ' ', COALESCE(chiled.housenum, ''), ' ', " .
				"COALESCE(addht1.short, ''), ' ', COALESCE(chiled.addnum1, ''), ' ', " .
				"COALESCE(addht2.short, ''), ' ', COALESCE(chiled.addnum2, '')" .
			")) as house",
			['mun' => MunHierarchy::table()]
		)->innerJoin(
			['chiled' => Houses::table()],
			['chiled' => 'objectid', 'mun' => 'chiledobjid_houses']
		)->leftJoin(
			['ht' => Housetype::table()],
			['ht' => 'id', 'chiled' => 'id_housetype']
		)->leftJoin(
			['addht1' => Addhousetype::table()],
			['addht1' => 'id', 'chiled' => 'id_addtype1']
		)->leftJoin(
			['addht2' => Addhousetype::table()],
			['addht2' => 'id', 'chiled' => 'id_addtype2']
		)->where(
			['mun' => 'parentobjid_addr'],
			$objectId
		)->save();

//		return $this->userStates['getHousesByObjectId']
//			->execute([$objectId]);
	}

	/**
	 * Return parent address object id by parent and chiled address name
	 * @param string $parentName - parent address name
	 * @param string $chiledName - chiled address name
	 * @return QueryResult
	 */
	public function findChainByParentAndChiledAddressName(string $parentName, string $chiledName): QueryResult
	{
		$parentName .= '%';
		$chiledName .= '%';

		return Database::select(
			'DISTINCT mun.parentobjid_addr, mun.chiledobjid_addr',
			['mun' => MunHierarchy::table()]
		)->innerJoin(
			['parent' => AddrObj::table()],
			['parent' => 'objectid', 'mun' => 'parentobjid_addr']
		)->leftJoin(
			['chiled' => AddrObj::table()],
			['chiled' => 'objectid', 'mun' => 'chiledobjid_addr']
		)->where(
			['parent' => 'id_level'],
			'<=',
			LEVEL
		)->andWhere(
			fn($builder) =>
			$builder->where(fn($builder) =>
				$builder->where(
					"CONCAT(parent.name, ' ', parent.typename)",
					'LIKE',
					$parentName
				)->orWhere(
					"CONCAT(parent.typename, ' ',parent.name)",
					'LIKE',
					$parentName
				)
			)->andWhere(fn($builder) =>
				$builder->where(
					"CONCAT(chiled.name, ' ', chiled.typename)",
					'LIKE',
					$chiledName
				)->orWhere(
					"CONCAT(chiled.typename, ' ', chiled.name)",
					'LIKE',
					$chiledName
				)
			)
		)->limit(2)->save();

//		return $this->userStates['findChainByParentAndChiledAddressName']
//			->execute([LEVEL, $parentName, $parentName, $chiledName, $chiledName]);
	}

	/**
	 * Return like address name by address name fragment
	 * @param string $halfAddress - address name fragment
	 * @return QueryResult
	 */
	public function getLikeAddress(string $halfAddress): QueryResult
	{
		$halfAddress .= '%';

		return Database::select(
			['addr' => ['name', 'typename', 'objectid']],
			['addr' => AddrObj::table()]
		)->where(
			['addr' => 'id_level'],
			'<=',
			LEVEL
		)->andWhere(fn($builder) =>
			$builder->where(
				"CONCAT(addr.name, ' ', addr.typename)",
				'LIKE',
				$halfAddress
			)->orWhere(
				"CONCAT(addr.typename, ' ', addr.name)",
				'LIKE',
				$halfAddress
			)
		)->limit(100)->save();

//		return $this->userStates['getLikeAddress']->execute([LEVEL, $halfAddress, $halfAddress]);
	}

	/**
	 * @param int $objectId
	 * @param string $type
	 * @return QueryResult
	 */
	function findAddrObjParamByObjectIdAndType(int $objectId, string $type): QueryResult
	{
		return Database::select(
			['params' => 'value'],
			['params' => AddrObjParams::table()]
		)->where(
			['params' => 'objectid_addr'],
			$objectId
		)->andWhere(
			['params' => 'type'],
			$type
		)->limit(1)->save();
	}

}
