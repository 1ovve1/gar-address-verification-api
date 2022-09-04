<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;

use DB\ORM\DBFacade;

trait InsertTrait
{
	public static function insert(array $fields_values,
	                              ?string $tableName = null): InsertQuery
	{
		[$fields, $values] = prepareArgsIntoFieldsAndValues($fields_values);
		$tableName ??= self::table();

		return new ImplInsert($fields, $values, $tableName);
	}


}

function prepareArgsIntoFieldsAndValues(array $fields_values) : array
{
	$fields = [];
	$values = [];

	if (is_string(key($fields_values))) {
		$fields = array_keys($fields_values);
		$values = normalizeValues(array_values($fields_values));

	} else {
		DBFacade::dumpException(null, 'Incorrect contract', func_get_args());
	}

	return [$fields, $values];
}

/**
 * @param array $values
 * @return array<array<mixed>>
 */
function normalizeValues(array $values) : array
{
	$normalized = [];
	$startElem = current($values);
	$maxCount = is_array($startElem) ? count($startElem): 1;

	$flagOfChanges = false;

	foreach ($values as $elem) {
		$currCount = match(is_array($elem)) {
			true => count($elem),
			false => 1
		};

		if ($currCount !== $maxCount) {
			if ($currCount > $maxCount) {
				$maxCount = $currCount;
			}

			$flagOfChanges = true;
		}

	}

	if($flagOfChanges) {
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
	}

	return $normalized;
}