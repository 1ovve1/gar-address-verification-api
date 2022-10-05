<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Join;

use PHPUnit\Framework\TestCase;

use DB\ORM\Resolver\DBResolver;
use DB\ORM\QueryBuilder\QueryTypes\Join\JoinAble;

class TestJoinTable extends JoinMock {}

class JoinTest extends TestCase
{
	public JoinAble $builder;

	function setUp(): void
	{
		$this->builder = new TestJoinTable();
	}

	const INPUT_TABLE_DRY = 'table';
	const INPUT_TABLE_CLEAN = ['table'];
	const INPUT_TABLE_MAPPED = ['tablename' => 'table'];

	const INPUT_VALUES_CLEAN = ['field1', 'field2'];
	const INPUT_VALUES_MAPPED = ['name1' => 'field1', 'name2' => 'field2'];

	const INPUT = [
		[self::INPUT_TABLE_DRY, self::INPUT_VALUES_CLEAN],
		[self::INPUT_TABLE_DRY, self::INPUT_VALUES_MAPPED],

		[self::INPUT_TABLE_CLEAN, self::INPUT_VALUES_CLEAN],
		[self::INPUT_TABLE_CLEAN, self::INPUT_VALUES_MAPPED],

		
		[self::INPUT_TABLE_MAPPED, self::INPUT_VALUES_CLEAN],
		[self::INPUT_TABLE_MAPPED, self::INPUT_VALUES_MAPPED],
	];

	const MYSQL_EXPECTED_INNER = [
		"INNER JOIN table ON `field1` = `field2`",
		"INNER JOIN table ON `name1`.`field1` = `name2`.`field2`",
	
		"INNER JOIN `table` ON `field1` = `field2`",
		"INNER JOIN `table` ON `name1`.`field1` = `name2`.`field2`",

		"INNER JOIN `table` as `tablename` ON `field1` = `field2`",
		"INNER JOIN `table` as `tablename` ON `name1`.`field1` = `name2`.`field2`",
	];

	function testInnerJoin(): void
	{
		foreach (self::INPUT as $case => [$table, $fields]) {
			$queryBox = $this->builder->innerJoin($table, $fields)->queryBox;

			$this->assertEquals(DBResolver::fmtSep() . self::MYSQL_EXPECTED_INNER[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");
		}	
	}

	const MYSQL_EXPECTED_LEFT = [
		"LEFT OUTER JOIN table ON `field1` = `field2`",
		"LEFT OUTER JOIN table ON `name1`.`field1` = `name2`.`field2`",
	
		"LEFT OUTER JOIN `table` ON `field1` = `field2`",
		"LEFT OUTER JOIN `table` ON `name1`.`field1` = `name2`.`field2`",

		"LEFT OUTER JOIN `table` as `tablename` ON `field1` = `field2`",
		"LEFT OUTER JOIN `table` as `tablename` ON `name1`.`field1` = `name2`.`field2`",
	];

	function testLeftJoin(): void
	{
		foreach (self::INPUT as $case => [$table, $fields]) {
			$queryBox = $this->builder->leftJoin($table, $fields)->queryBox;

			$this->assertEquals(DBResolver::fmtSep() . self::MYSQL_EXPECTED_LEFT[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");
		}	
	}

	const MYSQL_EXPECTED_RIGHT = [
		"RIGHT OUTER JOIN table ON `field1` = `field2`",
		"RIGHT OUTER JOIN table ON `name1`.`field1` = `name2`.`field2`",
	
		"RIGHT OUTER JOIN `table` ON `field1` = `field2`",
		"RIGHT OUTER JOIN `table` ON `name1`.`field1` = `name2`.`field2`",

		"RIGHT OUTER JOIN `table` as `tablename` ON `field1` = `field2`",
		"RIGHT OUTER JOIN `table` as `tablename` ON `name1`.`field1` = `name2`.`field2`",
	];

	function testRightJoin(): void
	{
		foreach (self::INPUT as $case => [$table, $fields]) {
			$queryBox = $this->builder->rightJoin($table, $fields)->queryBox;

			$this->assertEquals(DBResolver::fmtSep() . self::MYSQL_EXPECTED_RIGHT[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");
		}	
	}

	function testTooManyTables(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->builder->innerJoin(['test', 'wewe'], ['field1', 'field2']);

	}

	function testTooManyFields(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->builder->innerJoin('test', ['field1', 'field2', 'field3']);

	}
}