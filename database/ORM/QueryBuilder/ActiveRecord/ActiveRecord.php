<?php

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\Exceptions\BadQueryResultException;
use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\QueryResult;

/**
 * ActiveRecord interface
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
interface ActiveRecord
{
	/**
	 * Execute state with name by $values
	 *
	 * @param array<DatabaseContract> $values - values to execute
	 * @return QueryResult
	 * @throws BadQueryResultException
	 */
	public function execute(array $values): QueryResult;

	/**
	 * Execute query using state that was included into instruction
	 *
	 * @return QueryResult
	 * @throws BadQueryResultException
	 */
	public function save(): QueryResult;

	/**
	 * Return immutable queryBox of current object
	 * @return QueryBox
	 */
	public function getQueryBox(): QueryBox;
}