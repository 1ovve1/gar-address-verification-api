<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter;

/**
 * Common query tempalte interface for prepared statements
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
	 */
    public function exec(array $values): QueryResult;

    /**
     * Accept changes in template (use for lazy insert)
     *
     * @return QueryResult
     */
    public function save(): QueryResult;
}
