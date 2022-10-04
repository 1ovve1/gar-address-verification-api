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

	/** @var array<string|int, string|array<string>> */
	private array $fieldsWithPseudonyms = [
		'addr' => [
			'one', 'two'
		],
		'house' => 'three',
		'four',
		'five',
	];
	const STRING_FIELDS_WITH_PARAMS_RESULT = '`addr`.`one`, `addr`.`two`, `house`.`three`, `four`, `five`';

	function testFieldsWithPseudonymsToString(): void
	{
		$result = DBFacade::fieldsWithPseudonymsToString($this->fieldsWithPseudonyms);
		$this->assertEquals(self::STRING_FIELDS_WITH_PARAMS_RESULT, $result);
	}

	const STRING_TABLENAMES_WITH_PSEUDONYMS_RESULT = '`addr_obj` as `addr`, `houses` as `house`, `four`, `five`';
	/** @var array<string|int, string|array<string>> */
	private array $tableNamesWithPseudonyms = [
		'addr' => 'addr_obj',
		'house' => 'houses',
		'four',
		'five',
	];

	function testTableNamesWithPseudonymsToString(): void
	{
		$result = DBFacade::tableNamesWithPseudonymsToString($this->tableNamesWithPseudonyms);
		$this->assertEquals(self::STRING_TABLENAMES_WITH_PSEUDONYMS_RESULT, $result);
	}

	const TABLE_NAME = ['pseudonym1' => 'table'];
	const JOIN_ARGS_PSEUDONYM = [
        'pseudonym1' => 'field1',
        'pseudonym2' => 'field2',
    ];
	const JOIN_ARGS_JUST_FIELDS = [ 'pseudonym1.field1', 'pseudonym2.field2' ];
	const JOIN_ARGS_JUST_FIELDS_WRAPPED = [ '`pseudonym1`.`field1`', '`pseudonym2`.`field2`' ];
	const JOIN_ARGS_JUST_FIELDS_ASSOC = [ 'pseudonym1.field1' => 'pseudonym2.field2' ];
	const JOIN_ARGS_RESULT = ['tableName' => '`table` as `pseudonym1`', 'condition' => self::JOIN_ARGS_JUST_FIELDS];
	const JOIN_ARGS_RESULT_WRAPPED = ['tableName' => '`table` as `pseudonym1`', 'condition' => self::JOIN_ARGS_JUST_FIELDS_WRAPPED];

	function JoinArgsHandler(): void
	{
		$result1 = DBFacade::joinArgsHandler(self::TABLE_NAME, self::JOIN_ARGS_PSEUDONYM);
		$result2 = DBFacade::joinArgsHandler(self::TABLE_NAME, self::JOIN_ARGS_JUST_FIELDS);
		$result3 = DBFacade::joinArgsHandler(self::TABLE_NAME,self::JOIN_ARGS_JUST_FIELDS_ASSOC);

		$this->assertEquals(self::JOIN_ARGS_RESULT, $result1);
		$this->assertEquals(self::JOIN_ARGS_RESULT, $result2);
		$this->assertEquals(self::JOIN_ARGS_RESULT, $result3);
	}
}