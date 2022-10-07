<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Select;

use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class SelectTest extends QueryTypesTestCase
{
	const INPUT_FIELDS_DRY = "first.data, second.data, data3";
	const INPUT_FIELDS_CLEAR = ["first" => "data1", "second" => "data2", "data3"];

	const INPUT_TABLES_DRY_MANY = 'test as select_mock, another_test_select_table';
	const INPUT_TABLES_CLEAR_MANY = ['test' => 'select_mock', 'another_test_select_table'];
	const INPUT_TABLES_NONE = null;
	

	const INPUT = [
		[self::INPUT_FIELDS_DRY, self::INPUT_TABLES_DRY_MANY],
		[self::INPUT_FIELDS_DRY, self::INPUT_TABLES_CLEAR_MANY],
		[self::INPUT_FIELDS_DRY, self::INPUT_TABLES_NONE],
		[self::INPUT_FIELDS_CLEAR, self::INPUT_TABLES_DRY_MANY],
		[self::INPUT_FIELDS_CLEAR, self::INPUT_TABLES_CLEAR_MANY],
		[self::INPUT_FIELDS_CLEAR, self::INPUT_TABLES_NONE],
	];

	const MYSQL_EXPECTED = [
		"SELECT first.data, second.data, data3 FROM test as select_mock, another_test_select_table",
		"SELECT first.data, second.data, data3 FROM `select_mock` as `test`, `another_test_select_table`",
		"SELECT first.data, second.data, data3 FROM `select_mock`",
		"SELECT `first`.`data1`, `second`.`data2`, `data3` FROM test as select_mock, another_test_select_table",
		"SELECT `first`.`data1`, `second`.`data2`, `data3` FROM `select_mock` as `test`, `another_test_select_table`",
		"SELECT `first`.`data1`, `second`.`data2`, `data3` FROM `select_mock`",
	];

	function testSelect(): void
	{
		foreach (self::INPUT as $case => [$fields, $table]) {
			$queryBox = SelectMock::select($fields, $table)->queryBox;

			$this->compare(self::MYSQL_EXPECTED[$case], $queryBox);
			
		}
	}
}