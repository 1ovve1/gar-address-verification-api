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
class AddrObjParams extends ConcreteTable implements QueryModel
{
	#[ArrayShape(['id_addr_params' => "string[]",
    'objectid_addr_params' => "string[]",
      'TYPE' => "string[]",
      'VALUE' => "string[]"])]
  public function fieldsToCreate() : ?array
	{
		return [
			'id_addr_params' => [
        'BIGINT UNSIGNED NOT NULL',
			],
			'objectid_addr_params' => [
        'BIGINT UNSIGNED NOT NULL',
			],
			'TYPE' => [
				'CHAR(5) NOT NULL',
			],
			'VALUE' => [
        'BIGINT UNSIGNED NOT NULL',
			],
		];
	}
}