<?php

namespace DB\ORM\Table\Utils;

use DB\ORM\Table\SQL\EndQuery;

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

	/**
	 * Doing forceInsert into template
	 *
	 * @param array<DatabaseContract> $values - values for the force insert
	 * @param String[]|null $fields - optional fields list fow more specific usage
	 * @return EndQuery
	 */
	public static function forceInsert(array $values, ?array $fields = null): EndQuery;
}