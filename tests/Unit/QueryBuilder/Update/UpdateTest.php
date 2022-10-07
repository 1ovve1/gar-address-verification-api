<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Update;

use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class UpdateTest extends QueryTypesTestCase
{
	const INPUT_FIELD = "first";
	
	const INPUT_DATA = "sample_data";
	
	const INPUT_TABLE_DRY = "update_mock";
	const INPUT_TABLE_NONE = null;

	const INPUT = [
		[self::INPUT_FIELD, self::INPUT_DATA, self::INPUT_TABLE_DRY],
		[self::INPUT_FIELD, self::INPUT_DATA, self::INPUT_TABLE_NONE],
	];

	const MYSQL_EXPECTED = [
		"UPDATE update_mock SET `first` = (?)",
		"UPDATE `update_mock` SET `first` = (?)",
	];

	const ARGS_EXPECTED = [
		["sample_data"],
		["sample_data"],
	];

	function testUpdate(): void
	{
		foreach (self::INPUT as $case => [$fields, $data, $table]) {
			$queryBox = UpdateMock::update($fields, $data, $table)->queryBox;

			$this->compare(self::MYSQL_EXPECTED[$case], $queryBox, self::ARGS_EXPECTED[$case]);
		}
	}
}