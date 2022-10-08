<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;

/**
 * Insert module
 */
interface InsertAble
{
	/**
	 * Create insert template
	 *
	 * @param array<string, DatabaseContract|array<DatabaseContract>> $fields_values
	 * @param string|null $tableName - name of table
	 * @return InsertQuery
	 */
	public static function insert(array $fields_values,
	                              ?string $tableName = null): InsertQuery;
}