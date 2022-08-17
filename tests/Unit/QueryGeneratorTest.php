<?php declare(strict_types=1);

namespace Tests\Unit;

use DB\ORM\Migration\Container\QueryGenerator;
use PHPUnit\Framework\TestCase;

class QueryGeneratorTest extends TestCase
{
	private const CREATE_QUERY = 'CREATE TABLE table_name (id INT, disc CHAR(50) NOT NULL, id_addr INT, FOREIGN KEY (id_addr) REFERENCES addr_obj (id))';
	private const TABLE_NAME = 'table_name';
	private const TABLE_PARAMS = [
		'fields' => [
			'id' => 'INT',
			'disc' => 'CHAR(50) NOT NULL',
			'id_addr' => 'INT'
		],
		'foreign' => [
			'id_addr' => 'addr_obj (id)'
		]
	];

	public function testMakeCreateTableQuery()
	{
		$query = QueryGenerator::makeCreateTableQuery(self::TABLE_NAME, self::TABLE_PARAMS);

		$this->assertEquals(self::CREATE_QUERY, $query);
	}
}
