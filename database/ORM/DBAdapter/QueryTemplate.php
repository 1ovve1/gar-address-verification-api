<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

interface QueryTemplate
{
	/**
	 * Execute statement
	 *
	 * @param ?array<int|string, DatabaseContract> $values - values to execute
	 * @return QueryResult
	 */
	public function exec(?array $values = null): QueryResult;

	/**
	 * Accept changes in template (use for lazy insert)
	 *
	 * @return QueryResult
	 */
	public function save(): QueryResult;
}