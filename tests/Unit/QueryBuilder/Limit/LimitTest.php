<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Limit;

use PHPUnit\Framework\TestCase;

use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitAble;
use Tests\Unit\QueryBuilder\QueryTypesTestCase;

class LimitTest extends QueryTypesTestCase
{
	public LimitAble $builder;

	function setUp(): void
	{
		$this->builder = new LimitMock();
	}

	function testNegativeValueExceptionTest(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->builder->limit(-10);
	}
}