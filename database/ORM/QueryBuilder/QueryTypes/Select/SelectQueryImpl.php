<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\EndQuery\LimitImpl;
use DB\ORM\QueryBuilder\QueryTypes\EndQuery\OrderByImpl;
use DB\ORM\QueryBuilder\Templates\SQL;

class SelectQueryImpl extends SelectQuery
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





	/**
	 * @inheritDoc
	 */
	public function limit(int $count): LimitImpl
	{
		if ($count <= 0) {
			DBFacade::dumpException($this, '$count should be 1 or higer', func_get_args());
		}

		return new LimitImpl($this, $count);
	}

	/**
	 * @inheritDoc
	 */
	public function orderBy(string $field, bool $asc = true): OrderByImpl
	{
		return new OrderByImpl($this, $field, $asc);
	}


}