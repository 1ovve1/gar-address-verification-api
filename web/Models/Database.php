<?php

declare(strict_types=1);

namespace GAR\Models;

use QueryBox\DBAdapter\QueryResult;
use QueryBox\QueryBuilder\QueryBuilder;

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
	 * Return single name address name by objectid param of concrete address
	 * @param int $objectId - object id concrete address
	 * @param int $region
	 * @return QueryResult
	 */
	public function getAddressByObjectId(int $objectId, int $region): QueryResult
	{
		return Database::select(
					['addr' => ['objectid', 'name'], 'typename' => ['short', 'desc']],
					['addr' => AddrObj::table()]
				)->innerJoin(
					['typename' => AddrObjTypename::table()],
					['typename' => 'id', 'addr' => 'id_typename']
				)->where(
					['addr' => 'region'], $region
				)->andWhere(
					['addr' => 'objectid'], $objectId
				)->save();

	}

	/**
	 * Return chiled name of using parent objectid and chiled name fragment
	 * @param int $parentObjectId - parent address objectid
	 * @param string $chiledName - chiled name fragment
	 * @param int $region
	 * @return QueryResult
	 */
	public function getChiledAddressByParentObjectIdAndChiledAddressName(int $parentObjectId, string $chiledName, int $region): QueryResult
	{
		$chiledName .= '%';

		return Database::select(
					['chiled' => ['objectid', 'name'], 'typename' => ['short', 'desc']],
					['map' => AddrObjByAddrObjHierarchy::table()]
				)->innerJoin(
					['chiled' => AddrObj::table()],
					['chiled' => 'objectid', 'map' => 'chiledobjid_addr']
				)->innerJoin(
					['typename' => AddrObjTypename::table()],
					['typename' => 'id', 'chiled' => 'id_typename']
				)->where(
					['map' => 'region'], $region
				)->andWhere(
					['map' => 'parentobjid_addr'], $parentObjectId
				)->andWhere(fn($builder) =>
					$builder->where(
						"CONCAT(chiled.name, ' ', typename.short)", 'LIKE', $chiledName
					)->orWhere(
						"CONCAT(typename.short, ' ', chiled.name)", 'LIKE', $chiledName
					)
				)->save();

	}

	/**
	 * Return parent name using chiled address objectid
	 * @param int $chiledObjectId - chiled address objectid
	 * @param int $region
	 * @return QueryResult
	 */
	public function getParentAddressByChiledObjectId(int $chiledObjectId, int $region): QueryResult
	{
		return Database::select(
					['parent' => ['objectid', 'name'], 'typename' => ['short', 'desc']],
					['map' => AddrObjByAddrObjHierarchy::table()]
				)->innerJoin(
					['parent' => AddrObj::table()],
					['parent' => 'objectid', 'map' => 'parentobjid_addr']
				)->innerJoin(
					['typename' => AddrObjTypename::table()],
					['typename' => 'id', 'parent' => 'id_typename']
				)->where(
					['map' => 'region'], $region
				)->andWhere(
					['map' => 'chiledobjid_addr'], $chiledObjectId
				)->save();

	}

	/**
	 * Return houses object id using parent address objectid
	 * @param int $objectId - parent address objectid
	 * @param int $region
	 * @param string $houseName
	 * @return QueryResult
	 */
	public function getHousesByParentObjectId(int $objectId, int $region, string $houseName = ''): QueryResult
	{
		$houseName = "%$houseName%";

		return Database::select(['houses' => 'house'], [
			'houses' => fn() => Database::select(
				"TRIM(' ' FROM CONCAT(COALESCE(ht.short, ''), ' ', COALESCE(chiled.housenum, ''), ' ', COALESCE(addht1.short, ''), ' ', COALESCE(chiled.addnum1, ''), ' ', COALESCE(addht2.short, ''), ' ', COALESCE(chiled.addnum2, ''))) as house",
				['map' => HousesByAddrObjHierarchy::table()]
			)->innerJoin(
				['chiled' => Houses::table()],
				['chiled' => 'objectid', 'map' => 'chiledobjid_houses']
			)->leftJoin(
				['ht' => HousesType::table()],
				['ht' => 'id', 'chiled' => 'id_type']
			)->leftJoin(
				['addht1' => HousesAddtype::table()],
				['addht1' => 'id', 'chiled' => 'id_addtype1']
			)->leftJoin(
				['addht2' => HousesAddtype::table()],
				['addht2' => 'id', 'chiled' => 'id_addtype2']
			)->where(
				['map' => 'region'], $region
			)->andWhere(
				['map' => 'parentobjid_addr'], $objectId
			)
		])->where(
			['houses' => 'house'], 'LIKE', $houseName
		)->save();

	}

	/**
	 * Return parent address object id by parent and chiled address name
	 * @param string $parentName - parent address name
	 * @param string $chiledName - chiled address name
	 * @param int $region
	 * @return QueryResult
	 */
	public function findChainByParentAndChiledAddressName(string $parentName, string $chiledName, int $region): QueryResult
	{
		$parentName .= '%';
		$chiledName .= '%';

		return Database::select(
					'DISTINCT map.parentobjid_addr, map.chiledobjid_addr',
					['map' => AddrObjByAddrObjHierarchy::table()]
				)->innerJoin(
					['parent' => AddrObj::table()],
					['parent' => 'objectid', 'map' => 'parentobjid_addr']
				)->innerJoin(
					['parent_typename' => AddrObjTypename::table()],
					['parent_typename' => 'id', 'parent' => 'id_typename']
				)->leftJoin(
					['chiled' => AddrObj::table()],
					['chiled' => 'objectid', 'map' => 'chiledobjid_addr']
				)->innerJoin(
					['chiled_typename' => AddrObjTypename::table()],
					['chiled_typename' => 'id', 'chiled' => 'id_typename']
				)->where(
					['map' => 'region'], $region
				)->andWhere(
					['chiled' => 'id_level'], '<=', LEVEL
				)->andWhere(
					['parent' => 'id_level'], '<=', LEVEL
				)->andWhere(
					fn($builder) =>
					$builder->where(fn($builder) =>
						$builder->where(
							"CONCAT(parent.name, ' ', parent_typename.short)",
							'LIKE',
							$parentName
						)->orWhere(
							"CONCAT(parent_typename.short, ' ', parent.name)",
							'LIKE',
		                    $parentName
						)
					)->andWhere(fn($builder) =>
						$builder->where(
							"CONCAT(chiled.name, ' ', chiled_typename.short)",
							'LIKE',
		                    $chiledName
						)->orWhere(
							"CONCAT(chiled_typename.short, ' ', chiled.name)",
							'LIKE',
							$chiledName
						)
					)
				)->limit(2)->save();

	}

	/**
	 * Return like address name by address name fragment
	 * @param string $halfAddress - address name fragment
	 * @param int $region
	 * @return QueryResult
	 */
	public function getLikeAddress(string $halfAddress, int $region): QueryResult
	{
		$halfAddress .= '%';

		return Database::select(
					['addr' => ['objectid', 'name'], 'typename' => ['short', 'desc']],
					['addr' => AddrObj::table()]
				)->innerJoin(
					['typename' => AddrObjTypename::table()],
					['typename' => 'id', 'addr' => 'id_typename']
				)->where(
					['addr' => 'region'], $region
				)->andWhere(
					['addr' => 'id_level'],
					'<=',
		            LEVEL
				)->andWhere(fn($builder) =>
					$builder->where(
						"CONCAT(addr.name, ' ', typename.short)",
						'LIKE',
						$halfAddress
					)->orWhere(
						"CONCAT(typename.short, ' ', addr.name)",
						'LIKE',
						$halfAddress
					)
				)->limit(100)->save();

	}

	/**
	 * @param int $objectId
	 * @param string $code
	 * @param int $region
	 * @return QueryResult
	 */
	function findAddrObjParamByObjectIdAndType(int $objectId, string $code, int $region): QueryResult
	{
		return Database::select(
			['params' => 'value'],
			['params' => AddrObjParams::table()]
		)->innerJoin(
			['types' => AddrObjParamsTypes::table()],
			['types' => 'id', 'params' => 'id_types']
		)->where(
			['params' => 'region'], $region
		)->andWhere(
			['params' => 'objectid_addr'],
			$objectId
		)->andWhere(
			['types' => 'code'],
			'=',
			$code
		)->limit(1)->save();

	}

	/**
	 * @param int $objectId
	 * @param int $region
	 * @return QueryResult
	 */
	function findAllAddrObjParamByObjectId(int $objectId, int $region): QueryResult
	{
		return Database::select(
			['params' => 'value', 'types' => 'code'],
			['params' => AddrObjParams::table()]
		)->innerJoin(
			['types' => AddrObjParamsTypes::table()],
			['types' => 'id', 'params' => 'id_types']
		)->where(
			['params' => 'region'], $region
		)->andWhere(
			['params' => 'objectid_addr'],
			$objectId
		)->save();

	}

}
