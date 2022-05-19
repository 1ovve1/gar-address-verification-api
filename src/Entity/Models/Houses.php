<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;
use JetBrains\PhpStorm\ArrayShape;


/**
 * AS HOUSES CLASS-MODEL
 *
 * EXTENDS CONCRETE TABLE AND USING FOR COMMUNICATE
 * WITH TABLE 'address_info'
 */
class Houses extends ConcreteTable implements QueryModel
{
	#[ArrayShape(['id_houses' => "string[]",
    'objectid_houses' => "string[]",
    'objectguid_houses' => "string[]",
    'housenum_houses' => "string[]",
    'housetype_houses' => "string[]"])]
  public function fieldsToCreate() : ?array
	{
		return [
			'id_houses' => [
        'BIGINT UNSIGNED NOT NULL',
			],
			'objectid_houses' => [
        'BIGINT UNSIGNED NOT NULL',
			],
			'objectguid_houses' => [
        'CHAR(50) NOT NULL',
			],
			'housenum_houses' => [
				'CHAR(100)',
			],
			'housetype_houses' => [
				'CHAR(50)',
			],
		];
	}
}