<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;
use DB\ORM\DBAdapter\DBAdapter;

/**
 * Insert module
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
interface InsertAble
{
	/**
	 * Create insert template
	 *
	 * @param array<mixed> $fields_values
	 * @param string|null $tableName - name of table
	 * @return InsertQuery
	 */
	public static function insert(array $fields_values,
	                              ?string $tableName = null): InsertQuery;
}