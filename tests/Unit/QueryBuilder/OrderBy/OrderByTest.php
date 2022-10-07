<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\OrderBy;

use DB\ORM\QueryBuilder\QueryTypes\OrderBy\OrderByAble;
use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class OrderByTest extends QueryTypesTestCase
{
	public OrderByAble $builder;

	protected function setUp(): void
	{
		$this->builder = new OrderByMock();
	}


	const INPUT_FIELD_DRY = 'field';
	const INPUT_FIELD_CLEAR = ['field'];
	const INPUT_FIELD_MAPPED = ['test' => 'field'];

	const INPUT_SORT_ASC = true;
	const INPUT_SORT_DESC = false;

	const INPUT = [
		[self::INPUT_FIELD_DRY, self::INPUT_SORT_ASC],
		[self::INPUT_FIELD_DRY, self::INPUT_SORT_DESC],

		[self::INPUT_FIELD_CLEAR, self::INPUT_SORT_ASC],
		[self::INPUT_FIELD_CLEAR, self::INPUT_SORT_DESC],

		[self::INPUT_FIELD_MAPPED, self::INPUT_SORT_ASC],
		[self::INPUT_FIELD_MAPPED, self::INPUT_SORT_DESC],
	];

	const MYSQL_EXPECTED = [
		"ORDER BY field ASC",
		"ORDER BY field DESC",

		"ORDER BY `field` ASC",
		"ORDER BY `field` DESC",

		"ORDER BY `test`.`field` ASC",
		"ORDER BY `test`.`field` DESC",
	];

	function testOrderBy(): void
	{
		foreach (self::INPUT as $case => [$fields, $sort]) {
			$queryBox = $this->builder->orderBy($fields, $sort)->queryBox;

			$this->compare(self::MYSQL_EXPECTED[$case], $queryBox);
		}
	}
}