<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter;

use DB\Exceptions\BadQueryResultException;
use DB\Exceptions\FailedDBConnectionViaDSNException;
use DB\ORM\Migration\Container\Query;
use RuntimeException;

/**
 * Common interface for databse connection
 *
 * @phpstan-type DatabaseContract int|float|string|bool|null
 */
interface DBAdapter
{
	/**
	 * @param string $dbType - type name of curr db
	 * @param string $dbHost - db host
	 * @param string $dbName - db name
	 * @param string $dbPort - port
	 * @return self;
	 * @throws FailedDBConnectionViaDSNException
	 */
	static function connectViaDSN(string $dbType, string $dbHost,
	                              string $dbName, string $dbPort,
	                              string $dbUsername, string $dbPass): self;

	/**
	 * Execute custom query container
	 *
	 * @param Query $query - query container
	 * @return QueryResult
	 * @throws BadQueryResultException
	 */
    public function rawQuery(Query $query): QueryResult;
  

	/**
	 * Prepare query by template. Use execute for execute statement or getTemplate to get QueryTemplate onbect
	 *
	 * @param string $template - template
	 * @return QueryTemplate - self
	 */
    public function prepare(string $template): QueryTemplate;

	/**
	 * Prepare lazy insert template and
	 *
	 * @param string $tableName
	 * @param array<mixed> $fields - fields
	 * @param int $stagesCount - stages count
	 * @return QueryTemplate - prepared statement object
	 */
    public function getForceInsertTemplate(
        string $tableName,
        array $fields,
        int $stagesCount = 1
    ): QueryTemplate;
}
