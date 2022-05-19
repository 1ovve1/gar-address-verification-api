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
class AddrObj extends ConcreteTable implements QueryModel
{
  #[ArrayShape(['id_addr' => "string[]", 'objectid_addr' => "string[]", 'objectguid_addr' => "string[]", 'level_addr' => "string[]", 'name_addr' => "string[]", 'typename_addr' => "string[]", 'FOREIGN KEY (level_addr)' => "string[]"])]
  public function fieldsToCreate() : ?array
	{
		return [
			'id_addr' => [
				'BIGINT UNSIGNED NOT NULL',
			],
			'objectid_addr' => [
				'BIGINT UNSIGNED PRIMARY KEY NOT NULL',
			],
			'objectguid_addr' => [
				'CHAR(50) NOT NULL',
			],
      'level_addr' => [
        'TINYINT UNSIGNED NOT NULL'
      ],
			'name_addr' => [
				'VARCHAR(100) NOT NULL',
			],
			'typename_addr' => [
				'VARCHAR(100) NOT NULL',
			],
      'FOREIGN KEY (level_addr)' => [
        'REFERENCES obj_levels (id)'
      ]
		];
	}
}