<?php

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\DBAdapter\QueryResult;

/**
 * ActiveRecord interface
 */
interface ActiveRecord
{
	/**
	 * Execute state with name by $values
	 *
	 * @param array<DatabaseContract> $values - values to execute
	 * @return QueryResult
	 */
	public function execute(array $values): QueryResult;

	/**
	 * Execute query using state that was included into instruction
	 *
	 * @return QueryResult
	 */
	public function save(): QueryResult;

	/**
	 * Return immutable queryBox of current object
	 * @return QueryBox
	 */
	public function getQueryBox(): QueryBox;
}