<?php declare(strict_types=1);

namespace Tests\Unit;

use DB\ORM\QueryBuilder\QueryTypes\Insert\InsertTrait;
use PHPUnit\Framework\TestCase;

class InsertTraitTest extends TestCase
{
use InsertTrait;

	const VALUES_AND_FIELDS = [
		'ADD' => 2,
		'TWO' => [2, 3 ,4],
		'THREE' => [2]
	];
	const FIELDS = [
		'`ADD`',
		'`TWO`',
		'`THREE`'
	];
	const TRUE_VALUES = [
		2, 2, 2, null, 3, null, null, 4, null
	];

	function testNormalize(): void
	{
		$result = self::prepareArgsIntoFieldsAndValues(self::VALUES_AND_FIELDS);
		$this->assertEquals(['fields' => self::FIELDS, 'values' => self::TRUE_VALUES], $result);
	}
}