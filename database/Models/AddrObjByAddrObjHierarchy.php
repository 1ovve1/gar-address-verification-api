<?php declare(strict_types=1);

namespace DB\Models;

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
			'checkIfAddrObjExists' =>
				AddrObj::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),
		];
	}

	function checkIfAddrObjExists(int $region, int $addrObjId): bool
	{
		return $this->userStates['checkIfAddrObjExists']
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
				'chiledobjid_addr' => "BIGINT UNSIGNED NOT NULL",
				'region'        => 'TINYINT UNSIGNED NOT NULL',
			],
			'foreign' => [
				'parentobjid_addr'      => [AddrObj::class, 'objectid'],
				'chiledobjid_addr'      => [AddrObj::class, 'objectid'],
			]
		];
	}

}