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
			'id' => [
				'BIGINT UNSIGNED NOT NULL',
			],
			'objectid' => [
				'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
			],
			'objectguid' => [
				'CHAR(50) NOT NULL',
			],
      'id_level' => [
        'TINYINT UNSIGNED NOT NULL'
      ],
			'name' => [
				'VARCHAR(100) NOT NULL',
			],
			'typename' => [
				'VARCHAR(100) NOT NULL',
			],
      'FOREIGN KEY (id_level)' => [
        'REFERENCES obj_levels (id)'
      ]
		];
	}
}