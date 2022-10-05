<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;

use DB\ORM\QueryBuilder\QueryBuilder;
use RuntimeException;

trait InsertTrait
{
	/**
	 * @inheritDoc
	 * @param array<string, DatabaseContract|array<DatabaseContract>> $fields_values
	 */
	public static function insert(array $fields_values,
	                              ?string $tableName = null): InsertQuery
	{
		$tableName = match(null !== $tableName) {
			true => $tableName,
			default => self::tableQuoted()
		};

		['fields' => $fields, 'values' => $values] = self::prepareArgsIntoFieldsAndValues($fields_values);

		return new ImplInsert($fields, $values, $tableName);
	}

	/**
	 * @param array<int|string, DatabaseContract|array<DatabaseContract>> $fields_values
	 * @return array{fields: array<int, int|string>, values: array<int|string, DatabaseContract>}
	 */
	private static function prepareArgsIntoFieldsAndValues(array $fields_values) : array
	{
		if (is_string(key($fields_values))) {
			$fields = array_map(fn($x) => "`{$x}`", array_keys($fields_values));
			$values = self::normalizeValues(array_values($fields_values));

		} else {
			throw new RuntimeException('field values should have a string keys');
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

		foreach (array_values($values) as $coll => $row) {
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