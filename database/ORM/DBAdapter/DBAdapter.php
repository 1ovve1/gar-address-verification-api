<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter;

use DB\ORM\Migration\Container\Query;

/**
 * Common interface for databse connection
 *
 * @phpstan-type DatabaseContract int|float|string|bool|null
 */
interface DBAdapter
{
	public const PDO_F_ALL = \PDO::FETCH_ASSOC;
	public const PDO_F_COL = \PDO::FETCH_COLUMN;

	/**
	 * Execute custom query container
	 *
	 * @param Query $query - query container
	 * @return QueryResult
	 */
    public function rawQuery(Query $query): QueryResult;
  

	/**
	 * Preapre query by template. Use execute for execute statement or getTemplate to get QueryTemplate onbect
	 *
	 * @param string $template - template
	 * @return QueryTemplate - self
	 */
    public function prepare(string $template): QueryTemplate;

    /**
     * Prepare lazy insert template and
     *
     * @param  string $tableName- name of table
     * @param  array<mixed> $fields - fields
     * @param  int $stagesCount - stages count
     * @return QueryTemplate - prepared statement object
     */
    public function getForceInsertTemplate(
        string $tableName,
        array $fields,
        int $stagesCount = 1
    ): QueryTemplate;
}
