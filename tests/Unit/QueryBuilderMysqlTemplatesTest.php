<?php declare(strict_types=1);

namespace Tests\Integration;

use DB\ORM\QueryBuilder\QueryBuilder;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;

class AddrObj extends QueryBuilder {}

class QueryBuilderTest extends BaseTestSetup
{
	function testSimplSelect(): void
	{
		$db = DBFacade::getInstance();
		// $template = $db->prepare("SELECT * FROM addr_obj WHERE name = ?")->getTemplate();
		// $template = AddrObj::select('*')->where('name', 'Калмыкия');

		for ($iter = 0; $iter < 10; ++$iter) {
			// $result = $db->prepare("SELECT * FROM addr_obj WHERE name = ?")->getTemplate()->exec(['Калмыкия']);
			// $result = $template->exec(['Калмыкия']);	
			$result = AddrObj::select('*')
				->where(function($builder) {
					$builder->where('id_level', 2)
					->orWhere('id_level', 3);
				})->orWhere('name', 'Калмыкия')
				->limit(2)
				->execute([2, 3, 'Калмыкия']);
				// ->getQueryBox()->querySnapshot;
			// $result = $template->execute(['Калмыкия']);
		}


		$this->assertNotEmpty($result);
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