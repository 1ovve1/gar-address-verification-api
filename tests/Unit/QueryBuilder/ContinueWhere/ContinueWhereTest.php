<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\ContinueWhere;

use PHPUnit\Framework\TestCase;

use DB\ORM\Resolver\DBResolver;
use DB\ORM\Resolver\AST;
use DB\ORM\QueryBuilder\QueryTypes\ContinueWhere\ContinueWhereAble;
use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use Tests\Mock\FakeActiveRecordImpl;


defined('COND_EQ') ?:
	define('COND_EQ', DBResolver::cond_eq());

defined('CALLBACK_EMPTY_ACTIVE_RECORD') ?:
	define('CALLBACK_EMPTY_ACTIVE_RECORD', fn() => new FakeActiveRecordImpl("data in callback"));

class ContinueWhereTest extends TestCase
{
	public ContinueWhereAble $builder;

	function setUp(): void
	{
		$this->builder = new ContinueWhereMock();
	}

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

	const ARGS_EXPECTED = [
		["value"],
		["value in cond"],

		["value"],
		["value in cond"],

		["value"],
		["value in cond"],

		[],
	];

	const MYSQL_EXPECTED_AND = [
		"AND field = (?)",
		"AND field = (?)",

		"AND `field` = (?)",
		"AND `field` = (?)",

		"AND `test`.`field` = (?)",
		"AND `test`.`field` = (?)",

		"AND (data in callback)",
	];


	function testAndWhere(): void
	{
		foreach (self::INPUT as $case => [$field, $cond, $value]) {
			$queryBox = $this->builder->andWhere($field, $cond, $value)->queryBox;

			$this->assertEquals(DBResolver::fmtSep() . self::MYSQL_EXPECTED_AND[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");

			$this->assertEquals(self::ARGS_EXPECTED[$case], $queryBox->getDryArgs(), "Error in case {$case}");

		}
	}

	function testIncorrectAndWhereCondition(): void
	{
		$this->expectException(DriverImplementationNotFoundException::class);
		$queryBox = $this->builder->andWhere("field", null, "value")->queryBox;
	}

	const MYSQL_EXPECTED_OR = [
		"OR field = (?)",
		"OR field = (?)",

		"OR `field` = (?)",
		"OR `field` = (?)",

		"OR `test`.`field` = (?)",
		"OR `test`.`field` = (?)",

		"OR (data in callback)",
	];


	function testOrWhere(): void
	{
		foreach (self::INPUT as $case => [$field, $cond, $value]) {
			$queryBox = $this->builder->orWhere($field, $cond, $value)->queryBox;

			$this->assertEquals(DBResolver::fmtSep() . self::MYSQL_EXPECTED_OR[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");

			$this->assertEquals(self::ARGS_EXPECTED[$case], $queryBox->getDryArgs(), "Error in case {$case}");

		}
	}

	function testIncorrectOrWhereCondition(): void
	{
		$this->expectException(DriverImplementationNotFoundException::class);
		$queryBox = $this->builder->orWhere("field", null, "value")->queryBox;
	}
}