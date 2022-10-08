<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Insert;

use DB\ORM\Resolver\DBResolver;
use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class InsertTest extends QueryTypesTestCase
{
	const INPUT_FIELDS_MAPPED = ["first" => "data1", "second" => "data2"];
	const INPUT_FIELDS_MAPPED_MANY = ["first" => ["data1", "data2"], "second" => ["data3", "data4"]];

	const INPUT_TABLES_DRY = 'insert_mock';
	const INPUT_TABLES_NONE = null;
	
	const INPUT = [
		[self::INPUT_FIELDS_MAPPED, self::INPUT_TABLES_DRY],
		[self::INPUT_FIELDS_MAPPED, self::INPUT_TABLES_NONE],
		[self::INPUT_FIELDS_MAPPED_MANY, self::INPUT_TABLES_DRY],
		[self::INPUT_FIELDS_MAPPED_MANY, self::INPUT_TABLES_NONE],
	];

	const MYSQL_EXCPECTED = [
		"INSERT INTO insert_mock (`first`, `second`) VALUES (?, ?)",
		"INSERT INTO `insert_mock` (`first`, `second`) VALUES (?, ?)",
		"INSERT INTO insert_mock (`first`, `second`) VALUES (?, ?),(?, ?)",
		"INSERT INTO `insert_mock` (`first`, `second`) VALUES (?, ?),(?, ?)",
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
			$queryBox = InsertMock::insert($fields, $table)->queryBox;

			$this->compare(self::MYSQL_EXCPECTED[$case], $queryBox, self::ARGS_EXPECTED[$case]);
		}
	}
}