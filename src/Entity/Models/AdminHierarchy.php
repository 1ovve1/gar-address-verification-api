<?php declare(strict_types=1);

namespace GAR\Entity\Models;

use GAR\Database\ConcreteTable;
use GAR\Database\Table\SQL\QueryModel;


class AdminHierarchy extends ConcreteTable implements QueryModel
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
        'BIGINT UNSIGNED NOT NULL',

			'parentobjid_addr' =>
        'BIGINT UNSIGNED NOT NULL',

//      'FOREIGN KEY (objectid_admin)' =>
//        'REFERENCES addr_obj (objectid_addr),'
		];
	}
}