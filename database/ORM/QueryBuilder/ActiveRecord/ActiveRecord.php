<?php

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\QueryBuilder\AbstractSQL\EndQuery;

/**
 * ActiveRecord interface
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface ActiveRecord
{
	/**
	 * Execute state with name by $values
	 *
	 * @param  array<DatabaseContract> $values - values to execute
	 * @return array<mixed>
	 */
	public function execute(array $values): array;

	/**
	 * Execute query using state that was included into instruction
	 *
	 * @return array<mixed>
	 */
	public function save(): array;

	/** Return immutable queryBox of current object */
	public function getQueryBox(): QueryBox;
}