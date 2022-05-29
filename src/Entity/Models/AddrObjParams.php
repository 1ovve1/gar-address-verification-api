<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;


/**
 * ADDRESS INFO CLASS-MODEL
 *
 * EXTENDS CONCRETE TABLE AND USING FOR COMMUNICATE
 * WITH TABLE 'address_info'
 */
class AddrObjParams extends ConcreteTable implements QueryModel
{
  public function fieldsToCreate() : ?array
	{
		return [
			'id' => [
        'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
			],
			'objectid_addr' => [
        'BIGINT UNSIGNED NOT NULL',
			],
			'OKATO' => [
        'BIGINT UNSIGNED',
			],
      'OKTMO' => [
        'BIGINT UNSIGNED',
      ],
      'KLADR' => [
        'BIGINT UNSIGNED',
      ],
      'FOREIGN KEY (objectid_addr)' => [
        'REFERENCES addr_obj (objectid)'
      ]
		];
	}
}