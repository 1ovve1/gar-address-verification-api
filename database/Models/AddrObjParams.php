<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\QueryBuilder\QueryBuilder;


class AddrObjParams extends QueryBuilder 
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'getFirstObjectIdAddrObj' =>
				AddrObj::select('objectid')
				->where('region')
				->andWhere('objectid')
				->limit(1),
		];
	}

	public function getFirstObjectIdAddrObj(int $region, int $objectId): array
	{
		return $this->userStates['getFirstObjectIdAddrObj']
			->execute([$region, $objectId]);
	}

//    /**
//     * Return fields that need to create in model
//     *
//     * @return array<string, string>|null
//     */
//    public function fieldsToCreate(): ?array
//    {
//        return [
//            'objectid_addr' =>
//        'BIGINT UNSIGNED NOT NULL',
//
//            'type' =>
//              'CHAR(5) NOT NULL',
//
//            'value' =>
//              'CHAR(31) NOT NULL',
//
//            'region' =>
//              'TINYINT UNSIGNED NOT NULL',
//
//            'FOREIGN KEY (objectid_addr)' =>
//              'REFERENCES addr_obj (objectid)',
//        ];
//    }
}
