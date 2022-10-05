<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DB\ORM\QueryBuilder\QueryBuilder;
use DB\ORM\Resolver\DBResolver;

class TestUpdateTable extends QueryBuilder {}

class UpdateTest extends TestCase
{
	const INPUT_FIELD = "first";
	
	const INPUT_DATA = "sample_data";
	
	const INPUT_TABLE_DRY = "test_update_table";
	const INPUT_TABLE_NONE = null;

	const INPUT = [
		[self::INPUT_FIELD, self::INPUT_DATA, self::INPUT_TABLE_DRY],
		[self::INPUT_FIELD, self::INPUT_DATA, self::INPUT_TABLE_NONE],
	];

	const MYSQL_EXPECTED = [
		"UPDATE test_update_table SET `first` = (?)",
		"UPDATE `test_update_table` SET `first` = (?)",
	];

	const ARGS_EXPECTED = [
		["sample_data"],
		["sample_data"],
	];

	function testUpdate(): void
	{
		foreach (self::INPUT as $case => [$fields, $data, $table]) {
			$queryBox = TestUpdateTable::update($fields, $data, $table)->queryBox;

			$this->assertEquals(self::MYSQL_EXPECTED[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");
			$this->assertEquals(self::ARGS_EXPECTED[$case], $queryBox->getDryArgs(), "Error in case {$case}");
		}
	}
}