<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DB\ORM\QueryBuilder\QueryBuilder;
use DB\ORM\Resolver\DBResolver;

class TestInsertTable extends QueryBuilder {}

class InsertTest extends TestCase
{
	const INPUT_FIELDS_MAPPED = ["first" => "data1", "second" => "data2"];
	const INPUT_FIELDS_MAPPED_MANY = ["first" => ["data1", "data2"], "second" => ["data3", "data4"]];

	const INPUT_TABLES_DRY = 'test_insert_table';
	const INPUT_TABLES_NONE = null;
	
	const INPUT = [
		[self::INPUT_FIELDS_MAPPED, self::INPUT_TABLES_DRY],
		[self::INPUT_FIELDS_MAPPED, self::INPUT_TABLES_NONE],
		[self::INPUT_FIELDS_MAPPED_MANY, self::INPUT_TABLES_DRY],
		[self::INPUT_FIELDS_MAPPED_MANY, self::INPUT_TABLES_NONE],
	];

	const MYSQL_EXCPECTED = [
		"INSERT INTO test_insert_table (`first`, `second`) VALUES (?, ?)",
		"INSERT INTO `test_insert_table` (`first`, `second`) VALUES (?, ?)",
		"INSERT INTO test_insert_table (`first`, `second`) VALUES (?, ?),(?, ?)",
		"INSERT INTO `test_insert_table` (`first`, `second`) VALUES (?, ?),(?, ?)",
	];

	const ARGS_EXPECTED = [
		["data1", "data2"],
		["data1", "data2"],
		["data1", "data3", "data2", "data4"],
		["data1", "data3", "data2", "data4"],
	];

	function testInsert(): void
	{
		foreach (self::INPUT as $case => [$fields, $table]) {
			$queryBox = TestInsertTable::insert($fields, $table)->queryBox;

			$this->assertEquals(self::MYSQL_EXCPECTED[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");
			$this->assertEquals(self::ARGS_EXPECTED[$case], $queryBox->getDryArgs(), "Error in case {$case}");
		}
	}
}