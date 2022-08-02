<?php declare(strict_types=1);

namespace Tests\Unit;

use DB\ORM\QueryBuilder\QueryBuilder;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;
use PHPUnit\Framework\TestCase;

class AddrObj extends QueryBuilder {}

class QueryBuilderMysqlTemplatesTest extends TestCase
{
	const SELECT_RESULT = 'SELECT addr.one, addr.two, rose, house.free FROM AddrObj as addr, Houses as house';

	function testSelect(): void
	{
		$result = AddrObj::select([
			'addr' => [
				'one', 'two'
			],
			'rose',
			'house' => 'free'
		], [
			'addr' => 'AddrObj',
			'house' => 'Houses'
		])->getQueryBox()->querySnapshot;


		$this->assertEquals(self::SELECT_RESULT . SQL::SEPARATOR->value, $result);
	}

	const INSERT_RESULT = "INSERT INTO addr_obj (one, two, free) VALUES (?, ?, ?),(?, ?, ?)";
	private array $INSERT_DRY_INPUT = [
		'one' => [1, '3'],
		'two' => [2, 3],
		'free' => 4
	];
	const INSERT_DRY_ARGS =[
		1, 2, 4, '3', 3, null
	];

	function testInsertState(): void
	{
		$queryBox = AddrObj::insert(
			$this->INSERT_DRY_INPUT
		)->queryBox;
//		var_dump($queryBox->dryArgs);
		$this->assertEquals(self::INSERT_RESULT . SQL::SEPARATOR->value, $queryBox->querySnapshot);
		$this->assertEquals(self::INSERT_DRY_ARGS, $queryBox->dryArgs);
	}
}