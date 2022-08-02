<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\EndQuery\OrderByImpl;
use DB\ORM\QueryBuilder\Templates\SQL;

class ImplSelectQuery extends SelectQuery
{
	/**
	 * @param string $fields
	 * @param string $anotherTables
	 */
	function __construct(string $fields,
	                     string $anotherTables)
	{
		parent::__construct(
			$this::createQueryBox(
				template: SQL::SELECT, clearArgs: [$fields, $anotherTables]
			)
		);
	}

}