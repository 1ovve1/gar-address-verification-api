<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DB\ORM\QueryBuilder\QueryBuilder;
use DB\ORM\Resolver\DBResolver;

class TestDeleteTable extends QueryBuilder {}

class DeleteTest extends TestCase
{
	const INPUT_TABLE_DRY = "test_delete_table";
	const INPUT_TABLE_NONE = null;

	const INPUT = [
		[self::INPUT_TABLE_DRY],
		[self::INPUT_TABLE_NONE],
	];

	const MYSQL_EXPECTED = [
		"DELETE FROM test_delete_table",
		"DELETE FROM `test_delete_table`",
	];

	function testDelete(): void
	{
		foreach (self::INPUT as $case => [$table]) {
			$queryBox = TestDeleteTable::delete($table)->queryBox;

			$this->assertEquals(self::MYSQL_EXPECTED[$case] . DBResolver::fmtSep(), $queryBox->getQuerySnapshot(), "Error in case {$case}");
		}
	}
}