<?php declare(strict_types=1);

namespace Tests\Unit;

use DB\ORM\DBFacade;
use PHPUnit\Framework\TestCase;

class DBFacadeTest extends TestCase
{
	const CLASS_NAME = self::class;
	const DB_NAME = 'd_b_facade_test';

	function testClassNameToDBNameConverter(): void
	{
		$this->assertEquals(self::DB_NAME, DBFacade::genTableNameByClassName(self::CLASS_NAME));
	}
}