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
class MunHierarchy extends ConcreteTable implements QueryModel
{
  public function fieldsToCreate() : ?array
	{
		return [
			'id' => [
        'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
			],
			'parentobjid_addr' => [
        'BIGINT UNSIGNED NOT NULL',
      ],
      'chiledobjid_addr' => [
        'BIGINT UNSIGNED',
      ],
      'chiledobjid_houses' => [
        'BIGINT UNSIGNED',
      ],
      'FOREIGN KEY (parentobjid_addr)' => [
        'REFERENCES addr_obj (objectid)'
      ],
      'FOREIGN KEY (chiledobjid_addr)' => [
        'REFERENCES addr_obj (objectid)'
      ],
      'FOREIGN KEY (chiledobjid_houses)' => [
        'REFERENCES houses (objectid)'
      ],
		];
	}
}