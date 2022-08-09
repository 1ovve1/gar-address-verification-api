<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;
/**
 * Insert module
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface InsertAble
{
	/**
	 * Create insert template
	 *
	 * @param array $fields_values
	 * @param string|null $tableName - name of table
	 * @return InsertQuery
	 */
	public static function insert(array $fields_values,
	                              ?string $tableName = null): InsertQuery;
}