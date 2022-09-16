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
		];
	}

	function checkIfHousesObjExists(int $region, int $addrObjId): bool
	{
		return $this->userStates['checkIfHousesObjExists']
			->execute([$region, $addrObjId])
			->isNotEmpty();
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