<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;


/**
 * AS HOUSES CLASS-MODEL
 *
 * EXTENDS CONCRETE TABLE AND USING FOR COMMUNICATE
 * WITH TABLE 'address_info'
 */
class Houses extends ConcreteTable implements QueryModel
{
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
			'housenum' => [
				'VARCHAR(50)',
			],
      'addnum1' => [
        'VARCHAR(50)',
      ],
      'addnum2' => [
        'VARCHAR(50)',
      ],
			'id_housetype' => [
				'TINYINT UNSIGNED',
			],
      'id_addtype1' => [
        'TINYINT UNSIGNED',
      ],
      'id_addtype2' => [
        'TINYINT UNSIGNED',
      ],
      'FOREIGN KEY (id_housetype)' => [
        'REFERENCES housetype (id)'
      ],
      'FOREIGN KEY (id_addtype1)' => [
        'REFERENCES addhousetype (id)'
      ],
      'FOREIGN KEY (id_addtype2)' => [
        'REFERENCES addhousetype (id)'
      ],
		];
	}
}