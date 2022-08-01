<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

interface SelectAble
{
	/**
	 * Creating select template
	 *
	 * @param  array<string>|string $fields - fields to select
	 * @param  array<string>|string|null $anotherTables - name of another table
	 * @return SelectQuery
	 */
	public static function select(array|string $fields,
	                              null|array|string $anotherTables = null): SelectQuery;
}