<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

interface SelectAble
{
	/**
	 * Creating select template
	 *
	 * @param  array<string>|array<string, array<int, string>>|string $fields - fields to select
	 * @param  array<string>|array<int, string>|string|null $anotherTables - name of another table
	 * @return SelectQuery
	 */
	public static function select(string|array $fields,
	                              null|array|string $anotherTables = null): SelectQuery;
}