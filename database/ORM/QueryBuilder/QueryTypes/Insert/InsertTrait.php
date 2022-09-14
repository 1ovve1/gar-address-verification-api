<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryBuilder;

trait InsertTrait
{
	/**
	 * @inheritDoc
	 * @param array<int|string, DatabaseContract|array<DatabaseContract>> $fields_values
	 */
	public static function insert(array $fields_values,
	                              ?string $tableName = null): InsertQuery
	{
		['fields' => $fields, 'values' => $values] = self::prepareArgsIntoFieldsAndValues($fields_values);
		$tableName ??= QueryBuilder::table(static::class);

		return new ImplInsert($fields, $values, $tableName);
	}

	/**
	 * @param array<int|string, DatabaseContract|array<DatabaseContract>> $fields_values
	 * @return array{fields: array<string>, values: array<DatabaseContract>}
	 */
	private static function prepareArgsIntoFieldsAndValues(array $fields_values) : array
	{
		$fields = [];
		$values = [];

		if (is_string(key($fields_values))) {
			$fields = array_keys($fields_values);
			$values = self::normalizeValues(array_values($fields_values));

		} else {
			DBFacade::dumpException(null, 'Incorrect contract', func_get_args());
		}

		return ['fields' => $fields, 'values' => $values];
	}

	/**
	 * @param array<int|string, DatabaseContract|array<DatabaseContract>> $values
	 * @return array<int|string, DatabaseContract>
	 */
	private static function normalizeValues(array $values) : array
	{
		$normalized = [];
		$startElem = current($values);
		$maxCount = is_array($startElem) ? count($startElem): 1;


		foreach ($values as $elem) {
			$currCount = match(is_array($elem)) {
				true => count($elem),
				false => 1
			};

			if ($currCount > $maxCount) {
				$maxCount = $currCount;
			}

		}

		$stepSize = count($values);

		foreach ($values as $coll => $row) {
			if (!is_array($row)) {
				$row = [$row];
			}

			for ($index = 0; $index < $maxCount; ++$index) {

				$normalized[$coll + $index * $stepSize] = $row[$index] ?? null;
			}
		}
		ksort($normalized);

		return $normalized;
	}

}