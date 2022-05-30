<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;

class AddrObj extends ConcreteTable implements QueryModel
{
	/**
	 * Return fields that need to create in model
	 * 
	 * @return array<string, string>|null
	 */
  public function fieldsToCreate() : ?array
	{
		return [
			'id' =>
				'BIGINT UNSIGNED NOT NULL',
		
			'objectid' =>
				'BIGINT UNSIGNED NOT NULL PRIMARY KEY',
		
			'objectguid' =>
				'CHAR(50) NOT NULL',
    
      'id_level' =>
        'TINYINT UNSIGNED NOT NULL',
		
			'name' =>
				'VARCHAR(100) NOT NULL',
		
			'typename' =>
				'VARCHAR(100) NOT NULL',
    
      'FOREIGN KEY (id_level)' =>
        'REFERENCES obj_levels (id)',
		];
	}
}