<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Condition;

use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use DB\ORM\Resolver\DBResolver;
use PHPUnit\Framework\TestCase;
use Tests\Mock\FakeActiveRecordImpl;

defined('COND_EQ') ?:
	define('COND_EQ', DBResolver::cond_eq());
defined('CALLBACK_EMPTY_ACTIVE_RECORD') ?:
	define('CALLBACK_EMPTY_ACTIVE_RECORD', fn() => new FakeActiveRecordImpl("data in callback"));

class ConditionTest extends TestCase
{
	const INPUT_FIELD_DRY = 'field';
	const INPUT_FIELD_CLEAR = ['field'];
	const INPUT_FIELD_MAPPED = ['test' => 'field'];
	const INPUT_CALLBACK = CALLBACK_EMPTY_ACTIVE_RECORD;

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

		[self::INPUT_CALLBACK, null, null]
	];

	const MYSQL_EXPECTED = [
		"field = (?)",
		"field = (?)",

		"`field` = (?)",
		"`field` = (?)",

		"`test`.`field` = (?)",
		"`test`.`field` = (?)",

		"(data in callback)",
	];

	const ARGS_EXPECTED = [
		["value"],
		["value in cond"],

		["value"],
		["value in cond"],

		["value"],
		["value in cond"],

		[],
	];


	function testWhere(): void
	{
		foreach (self::INPUT as $case => [$field, $cond, $value]) {
			$queryBox = ConditionMock::where($field, $cond, $value)->queryBox;

			$this->assertEquals(self::MYSQL_EXPECTED[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");

			$this->assertEquals(self::ARGS_EXPECTED[$case], $queryBox->getDryArgs(), "Error in case {$case}");

		}
	}

	function testIncorrectWhereCondition(): void
	{
		$this->expectException(DriverImplementationNotFoundException::class);
		ConditionMock::where("field", null, "value")->queryBox;
	}
}
