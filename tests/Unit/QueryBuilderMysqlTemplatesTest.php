<?php declare(strict_types=1);

namespace Tests\Unit;

use DB\ORM\QueryBuilder\QueryBuilder;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\NestedCondition\ClientNestedCondition;
use DB\ORM\QueryBuilder\QueryTypes\NestedCondition\NestedConditionAble;
use DB\ORM\QueryBuilder\Templates\SQL;
use GAR\Database\Table\SQLClosureBuilder;
use PHPUnit\Framework\TestCase;

defined('SEPARATOR') ?:
	define('SEPARATOR', SQL::SEPARATOR->value);

class AddrObj extends QueryBuilder {}

class QueryBuilderMysqlTemplatesTest extends TestCase
{
	/**************************
	 * SELECT TEST
	 *
	 */
	const SELECT_RESULT = 'SELECT addr.one, addr.two, rose, house.free FROM AddrObj as addr, Houses as house' . SEPARATOR;

	function testSelect(): void
	{
		$result = AddrObj::select(
			['addr' => ['one', 'two'], 'rose', 'house' => 'free'],
			['addr' => 'AddrObj', 'house' => 'Houses']
		)->getQueryBox()->getQuerySnapshot();


		$this->assertEquals(self::SELECT_RESULT, $result);
	}

	/**************************
	 * WHERE TEST
	 *
	 */
	const WHERE_RESULT = 'WHERE (addr.one = (?) OR rose < (?)) AND (house.free > (?) AND addr.two <= (?)) OR (rose >= (?))' . SEPARATOR;
	const WHERE_DRY_ARGS = [2, 3, 2, 3, 2];

	function testWhereAndContinueWhereAndNestedWhere(): void
	{
		$result = AddrObj::select(
			['addr' => ['one', 'two'], 'rose', 'house' => 'free'],
			['addr' => 'AddrObj', 'house' => 'Houses']
		)->where(fn (ClientNestedCondition $builder) =>
			$builder->where(['addr' => 'one'], self::WHERE_DRY_ARGS[0])
				->orWhere('rose', '<', self::WHERE_DRY_ARGS[1])
		)->andWhere(fn (ClientNestedCondition $builder) =>
			$builder->where(['house' => 'free'], '>', self::WHERE_DRY_ARGS[2])
				->andWhere(['addr' => 'two'], '<=', self::WHERE_DRY_ARGS[3])
		)->orWhere(fn (ClientNestedCondition $builder) =>
			$builder->where(['rose'], '>=', self::WHERE_DRY_ARGS[4])
		)->queryBox;

		$this->assertEquals(self::SELECT_RESULT . self::WHERE_RESULT, $result->getQuerySnapshot());
		$this->assertEquals(self::WHERE_DRY_ARGS, $result->getDryArgs());
	}

	/**************************
	 * JOIN TEST
	 *
	 */
	const JOIN_RESULT = 'INNER JOIN table as t1 ON t1.field = rose LEFT OUTER JOIN table as t2 ON t2.field = addr.one RIGHT OUTER JOIN table ON field = free' . SEPARATOR;

	function testJoin(): void
	{
		$result = AddrObj::select(
			['addr' => ['one', 'two'], 'rose', 'house' => 'free'],
			['addr' => 'AddrObj', 'house' => 'Houses']
		)->innerJoin(
			['t1' => 'table'], ['t1' => 'field', 'rose']
		)->leftJoin(
			['t2' => 'table'], ['t2' => 'field', 'addr' => 'one']
		)->rightJoin(
			'table', ['field' => 'free']
		)->queryBox;

		$this->assertEquals(self::SELECT_RESULT . self::JOIN_RESULT, $result->getQuerySnapshot());
	}

	/**************************
	 * LIMIT TEST
	 *
	 */

	const LIMIT_RESULT = "LIMIT 10" . SEPARATOR;

	function testLimit(): void
	{
		$result = AddrObj::select(
			['addr' => ['one', 'two'], 'rose', 'house' => 'free'],
			['addr' => 'AddrObj', 'house' => 'Houses']
		)->limit(10)->queryBox;

		$this->assertEquals(self::SELECT_RESULT . self::LIMIT_RESULT, $result->getQuerySnapshot());
	}

	/**************************
	 * ORDER BY TEST
	 *
	 */

	const ORDER_BY_ASC_RESULT = "ORDER BY addr.one, addr.two, rose ASC" . SEPARATOR;
	const ORDER_BY_DESC_RESULT = "ORDER BY rose DESC" . SEPARATOR;

	function testOrderBy(): void
	{
		$selectState = AddrObj::select(
			['addr' => ['one', 'two'], 'rose', 'house' => 'free'],
			['addr' => 'AddrObj', 'house' => 'Houses']
		);

		$orderByAsc = $selectState->orderBy(
			['addr' => ['one', 'two'], 'rose']
		)->queryBox;

		$orderByDesc = $selectState->orderBy(
			'rose',
			false
		)->queryBox;

		$this->assertEquals(self::SELECT_RESULT . self::ORDER_BY_ASC_RESULT, $orderByAsc->getQuerySnapshot());
		$this->assertEquals(self::SELECT_RESULT . self::ORDER_BY_DESC_RESULT, $orderByDesc->getQuerySnapshot());
	}

	/**************************
	 * INSERT TEST
	 *
	 */
	const INSERT_RESULT = "INSERT INTO addr_obj (one, two, free) VALUES (?, ?, ?),(?, ?, ?)" . SEPARATOR;
	/** @var array<string, array<int, int|string>|int|string> */
	private array $INSERT_DRY_INPUT = [
		'one' => [1, '3'],
		'two' => [2, 3],
		'free' => 4
	];
	const INSERT_DRY_ARGS =[
		1, 2, 4, '3', 3, null
	];

	function testInsert(): void
	{
		$queryBox = AddrObj::insert(
			$this->INSERT_DRY_INPUT
		)->queryBox;

		$this->assertEquals(self::INSERT_RESULT, $queryBox->getQuerySnapshot());
		$this->assertEquals(self::INSERT_DRY_ARGS, $queryBox->getDryArgs());
	}

	/**************************
	 * UPDATE TEST
	 *
	 */
	const UPDATE_RESULT = "UPDATE addr_obj SET one = (?)" . SEPARATOR;
	const UPDATE_DRY_ARGS = [2];

	function testUpdate(): void
	{
		$result = AddrObj::update('one', 2)->queryBox;

		$this->assertEquals(self::UPDATE_RESULT, $result->getQuerySnapshot());
		$this->assertEquals(self::UPDATE_DRY_ARGS, $result->getDryArgs());
	}

	/**************************
	 * DELETE TEST
	 *
	 */
	const DELETE_RESULT = "DELETE FROM addr_obj" . SEPARATOR;

	function testDelete(): void
	{
		$result = AddrObj::delete()->queryBox;

		$this->assertEquals(self::DELETE_RESULT, $result->getQuerySnapshot());
	}
}