<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

interface SelectAble
{
	/**
	 * Creating select template
	 *
	 * @param  array<int|string, string> | array<string, array<string>> | string $fields - fields to select
	 * @param  array<int|string, string> | array<string, callable(): ActiveRecord> | string | null $anotherTables - name of another table(s) or callback return ActiveRecord for sub-select
	 * @return SelectQuery
	 */
	public static function select(array|string $fields,
	                              array|string|null $anotherTables = null): SelectQuery;
}