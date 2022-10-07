<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Where;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereAble;
use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use Tests\Mock\FakeActiveRecordImpl;
use Tests\Unit\QueryBuilder\QueryTypesTestCase;


class WhereTest extends QueryTypesTestCase
{
	public WhereAble $builder;

	function setUp(): void
	{
		$this->builder = new WhereMock();
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

	const MYSQL_EXPECTED = [
		"WHERE field = (?)",
		"WHERE field = (?)",

		"WHERE `field` = (?)",
		"WHERE `field` = (?)",

		"WHERE `test`.`field` = (?)",
		"WHERE `test`.`field` = (?)",
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
			$queryBox = $this->builder->where($field, $cond, $value)->queryBox;

			$this->compare(self::MYSQL_EXPECTED[$case], $queryBox, self::ARGS_EXPECTED[$case]);
		}
	}

	function testCallbackWhere(): void
	{
		$queryBox = $this->builder->where(fn() => new FakeActiveRecordImpl("value in callback"))->queryBox;

		$this->compare("WHERE (value in callback)", $queryBox, []);
	}

	function testIncorrectWhereCondition(): void
	{
		$this->expectException(DriverImplementationNotFoundException::class);
		$this->builder->where("field", null, "value");
	}
}