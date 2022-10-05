<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Limit;

use PHPUnit\Framework\TestCase;

use DB\ORM\Resolver\DBResolver;
use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitAble;


class TestLimitTable extends LimitMock {}

class LimitTest extends TestCase
{
	public LimitAble $builder;

	function setUp(): void
	{
		$this->builder = new TestLimitTable();
	}

	function testNegativeValueExceptionTest(): void
	{
		$this->expectException(\RuntimeException::class);
		$this->builder->limit(-10);
	}
}