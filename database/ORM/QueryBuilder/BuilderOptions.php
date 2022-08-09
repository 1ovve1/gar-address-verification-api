<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\AbstractSQL\EndQuery;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;


/**
 * Common interface for query builder
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface BuilderOptions
{
	/**
	 * Finding first element of $field collumn with $value compare
	 *
	 * @param  string $field - fields name
	 * @param  DatabaseContract $value - value for compare
	 * @param  string|null $anotherTable - table name
	 * @return array<mixed>
	 */
	public static function findFirst(string $field,
	                                 mixed $value,
	                                 ?string $anotherTable = null): array;

	/**
	 * Check if state $tryState exists and implement $stateInstruction if state does not exist
	 * @param mixed $tryState
	 * @param callable $stateInstruction
	 * @return ActiveRecord
	 */
	public static function createStateIfNotExist(mixed $tryState,
	                                             callable $stateInstruction): ActiveRecord;

}