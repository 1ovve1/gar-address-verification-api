<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter;

use DB\Exceptions\Unchecked\BadQueryResultException;

/**
 * Common query template interface for prepared statements
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
interface QueryTemplate
{
	/**
	 * Execute statement
	 *
	 * @param array<int|string, DatabaseContract> $values - values to execute
	 * @return QueryResult
	 * @throws BadQueryResultException
	 */
    public function exec(array $values = []): QueryResult;

    /**
     * Accept changes in template (use for lazy insert)
     *
     * @return QueryResult
     * @throws BadQueryResultException
     */
    public function save(): QueryResult;
}
