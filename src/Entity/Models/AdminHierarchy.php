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
class AdminHierarchy extends ConcreteTable implements QueryModel
{
  public function fieldsToCreate() : ?array
	{
		return [
			'id' => [
        'BIGINT UNSIGNED NOT NULL',
			],
			'objectid' => [
        'BIGINT UNSIGNED NOT NULL',
			],
			'parentobjid_addr' => [
        'BIGINT UNSIGNED NOT NULL',
			],
//      'FOREIGN KEY (objectid_admin)' => [
//        'REFERENCES addr_obj (objectid_addr)'
//      ],
		];
	}
}