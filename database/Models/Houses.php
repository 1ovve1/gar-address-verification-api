<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\QueryBuilder\QueryBuilder;


class Houses extends QueryBuilder 
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'getFirstObjectId' =>
				Houses::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),
		];
	}

	public function getFirstObjectId(int $region, int $objectid): array
	{
		return $this->userStates['getFirstObjectId']
			->execute([$region, $objectid]);
	}
//    /**
//     * Return fields that need to create in model
//     *
//     * @return array<string, string>|null
//     */
//    public function fieldsToCreate(): ?array
//    {
//        return [
////            'id' =>
////        'INT UNSIGNED NOT NULL',
//
//            'objectid' =>
//        'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
//
////            'objectguid' =>
////        'VARCHAR(36) NOT NULL',
//
//            'housenum' =>
//                'VARCHAR(50)',
//
//            'addnum1' =>
//              'VARCHAR(50)',
//
//            'addnum2' =>
//              'VARCHAR(50)',
//
//            'id_housetype' =>
//                'TINYINT UNSIGNED',
//
//            'id_addtype1' =>
//              'TINYINT UNSIGNED',
//
//            'id_addtype2' =>
//              'TINYINT UNSIGNED',
//
//            'region' =>
//              'TINYINT UNSIGNED NOT NULL',
//
//            'FOREIGN KEY (id_housetype)' =>
//              'REFERENCES housetype (id)',
//
//            'FOREIGN KEY (id_addtype1)' =>
//              'REFERENCES addhousetype (id)',
//
//            'FOREIGN KEY (id_addtype2)' =>
//              'REFERENCES addhousetype (id)',
//        ];
//    }
}
