<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;


class ImplSelect extends SelectQuery
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
				clearArgs: [$fields, $anotherTables]
			)
		);
	}

}