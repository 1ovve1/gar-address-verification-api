<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Delete;

use DB\ORM\Resolver\DBResolver;
use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class DeleteTest extends QueryTypesTestCase
{
	const INPUT_TABLE_DRY = "delete_mock";
	const INPUT_TABLE_NONE = null;

	const INPUT = [
		[self::INPUT_TABLE_DRY],
		[self::INPUT_TABLE_NONE],
	];

	const MYSQL_EXPECTED = [
		"DELETE FROM delete_mock",
		"DELETE FROM `delete_mock`",
	];

	function testDelete(): void
	{
		foreach (self::INPUT as $case => [$table]) {
			$queryBox = DeleteMock::delete($table)->queryBox;

			$this->compare(self::MYSQL_EXPECTED[$case], $queryBox);
		}
	}
}