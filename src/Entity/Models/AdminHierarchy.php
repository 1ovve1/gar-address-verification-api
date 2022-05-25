<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;
use JetBrains\PhpStorm\ArrayShape;


/**
 * ADDRESS INFO CLASS-MODEL
 *
 * EXTENDS CONCRETE TABLE AND USING FOR COMMUNICATE
 * WITH TABLE 'address_info'
 */
class AdminHierarchy extends ConcreteTable implements QueryModel
{
  #[ArrayShape(['id_admin' => "string[]",
    'objectid_admin' => "string[]",
    'parentobjid_admin' => "string[]",
    'FOREIGN KEY (objectid_admin)' => "string[]",
    'FOREIGN KEY (parentobjid_admin)' => "string[]"])]
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