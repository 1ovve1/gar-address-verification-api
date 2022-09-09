<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter;

/**
 * Common query template interface for prepared statements
 */
interface QueryTemplate
{
	/**
	 * Execute statement
	 *
	 * @param array<int|string, DatabaseContract> $values - values to execute
	 * @return QueryResult
	 */
    public function exec(array $values = []): QueryResult;

    /**
     * Accept changes in template (use for lazy insert)
     *
     * @return QueryResult
     */
    public function save(): QueryResult;
}
