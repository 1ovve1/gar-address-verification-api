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
	 * Doing forceInsert into template
	 *
	 * @param array<DatabaseContract> $values - values for the force insert
	 * @param String[]|null $fields - optional fields list fow more specific usage
	 * @return EndQuery
	 */
	public function forceInsert(array $values, ?array $fields = null): EndQuery;
}