<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\QueryBuilder\QueryBuilder;


class MunHierarchy extends QueryBuilder 
{
	/**
	 * @inheritDoc
	 */
	protected function prepareStates(): array
	{
		return [
			'getIdAddrObj' =>
				AddrObj::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),
			'getIdHouses' =>
				Houses::select('region')
					->where('region')
					->andWhere('objectid')
					->limit(1),

		];
	}

	/**
	 * {@inheritDoc}
	 */
	protected static function getFields(): ?array
	{
		return ['parentobjid_addr', 'chiledobjid_addr', 'chiledobjid_houses', 'region'];
	}

    /**
     * Return fields that need to create in model
     *
     * @return array<string, string>|null
     */
    public function fieldsToCreate(): ?array
    {
        return [
            'parentobjid_addr' =>
                'BIGINT UNSIGNED NOT NULL',

            'chiledobjid_addr' =>
              'BIGINT UNSIGNED',

            'chiledobjid_houses' =>
              'BIGINT UNSIGNED',

	        'region' =>
	            'TINYINT UNSIGNED NOT NULL',

           'FOREIGN KEY (parentobjid_addr)' =>
             'REFERENCES addr_obj (objectid)',

           'FOREIGN KEY (chiledobjid_addr)' =>
             'REFERENCES addr_obj (objectid)',

           'FOREIGN KEY (chiledobjid_houses)' =>
             'REFERENCES houses (objectid)',
        ];
    }
}
