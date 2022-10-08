<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Condition;

use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use DB\ORM\QueryBuilder\QueryTypes\Condition\ContinueConditionAble;
use Tests\Mock\FakeActiveRecordImpl;
use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class ContinueConditionTest extends QueryTypesTestCase
{
	public ContinueConditionAble $builder;

	function setUp(): void
	{
		$this->builder = new ContinueConditionMock();
	}

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

	const ARGS_EXPECTED = [
		["value"],
		["value in cond"],

		["value"],
		["value in cond"],

		["value"],
		["value in cond"],

	];

	const MYSQL_EXPECTED_AND = [
		"AND field = (?)",
		"AND field = (?)",

		"AND `field` = (?)",
		"AND `field` = (?)",

		"AND `test`.`field` = (?)",
		"AND `test`.`field` = (?)",
	];


	function testAndWhere(): void
	{
		foreach (self::INPUT as $case => [$field, $cond, $value]) {
			$queryBox = $this->builder->andWhere($field, $cond, $value)->queryBox;

			$this->compare(self::MYSQL_EXPECTED_AND[$case], $queryBox, self::ARGS_EXPECTED[$case]);
		}
	}

	function testCallbackAndWhere(): void
	{
		$queryBox = $this->builder->andWhere(fn() => new FakeActiveRecordImpl("value in callback"))->queryBox;

		$this->compare("AND (value in callback)", $queryBox, []);
	}

	function testIncorrectAndWhereCondition(): void
	{
		$this->expectException(DriverImplementationNotFoundException::class);
		$this->builder->andWhere("field", null, "value");
	}

	const MYSQL_EXPECTED_OR = [
		"OR field = (?)",
		"OR field = (?)",

		"OR `field` = (?)",
		"OR `field` = (?)",

		"OR `test`.`field` = (?)",
		"OR `test`.`field` = (?)",
	];


	function testOrWhere(): void
	{
		foreach (self::INPUT as $case => [$field, $cond, $value]) {
			$queryBox = $this->builder->orWhere($field, $cond, $value)->queryBox;

			$this->compare(self::MYSQL_EXPECTED_OR[$case], $queryBox, self::ARGS_EXPECTED[$case]);
		}
	}

	function testCallbackOrWhere(): void
	{
		$queryBox = $this->builder->orWhere(fn() => new FakeActiveRecordImpl("value in callback"))->queryBox;

		$this->compare("OR (value in callback)", $queryBox, []);
	}

	function testIncorrectOrWhereCondition(): void
	{
		$this->expectException(DriverImplementationNotFoundException::class);
		$this->builder->orWhere("field", null, "value");
	}
}