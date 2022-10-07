<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Condition;

use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use Tests\Mock\FakeActiveRecordImpl;
use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class ConditionTest extends QueryTypesTestCase
{
	const INPUT_FIELD_DRY = 'field';
	const INPUT_FIELD_CLEAR = ['field'];
	const INPUT_FIELD_MAPPED = ['test' => 'field'];

	const INPUT_CONDITION_DEFAULT = COND_EQ;
	const INPUT_CONDITION_VALUE = "value in cond";


	const INPUT_VALUE_DEFAULT = "value";
	const INPUT_VALUE_NULL = null;

	const INPUT = [
		[self::INPUT_FIELD_DRY, self::INPUT_CONDITION_DEFAULT, self::INPUT_VALUE_DEFAULT],
		[self::INPUT_FIELD_DRY, self::INPUT_CONDITION_VALUE, self::INPUT_VALUE_NULL],

		[self::INPUT_FIELD_CLEAR, self::INPUT_CONDITION_DEFAULT, self::INPUT_VALUE_DEFAULT],
		[self::INPUT_FIELD_CLEAR, self::INPUT_CONDITION_VALUE, self::INPUT_VALUE_NULL],

		[self::INPUT_FIELD_MAPPED, self::INPUT_CONDITION_DEFAULT, self::INPUT_VALUE_DEFAULT],
		[self::INPUT_FIELD_MAPPED, self::INPUT_CONDITION_VALUE, self::INPUT_VALUE_NULL],
	];

	const MYSQL_EXPECTED = [
		"field = (?)",
		"field = (?)",

		"`field` = (?)",
		"`field` = (?)",

		"`test`.`field` = (?)",
		"`test`.`field` = (?)",
	];

	const ARGS_EXPECTED = [
		["value"],
		["value in cond"],

		["value"],
		["value in cond"],

		["value"],
		["value in cond"],
	];


	function testWhere(): void
	{
		foreach (self::INPUT as $case => [$field, $cond, $value]) {
			$queryBox = ConditionMock::where($field, $cond, $value)->queryBox;

			$this->compare(self::MYSQL_EXPECTED[$case], $queryBox, self::ARGS_EXPECTED[$case]);
		}
	}

	function testCallbackWhere(): void
	{
		$queryBox = ConditionMock::where(fn() => new FakeActiveRecordImpl("value in callback"))->queryBox;

		$this->compare("(value in callback)", $queryBox, []);
	}

	function testIncorrectWhereCondition(): void
	{
		$this->expectException(DriverImplementationNotFoundException::class);
		ConditionMock::where("field", null, "value");
	}
}
