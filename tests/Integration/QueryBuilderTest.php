<?php declare(strict_types=1);

namespace Tests\Integration;

use DB\ORM\QueryBuilder\QueryBuilder;
use DB\ORM\DBFacade;

class AddrObj extends QueryBuilder {}

class QueryBuilderTest extends BaseTestSetup
{
	function testSimplSelect(): void
	{
		$db = DBFacade::getInstance();
		// $template = $db->prepare("SELECT * FROM addr_obj WHERE name = ?")->getTemplate();
		// $template = AddrObj::select('*')->where('name', 'Калмыкия');

		for ($iter = 0; $iter < 1000; ++$iter) {
			// $result = $db->prepare("SELECT * FROM addr_obj WHERE name = ?")->getTemplate()->exec(['Калмыкия']);
			// $result = $template->exec(['Калмыкия']);	
			$result = AddrObj::select('*')
				->where(function($builder) {
					$builder->where('id_level', 2)
					->orWhere('id_level', 3);
				})->orWhere('name', 'Калмыкия')
				->execute([2, 3, 'Калмыкия']);
				// ->getQueryBox()->querySnapshot;
			// $result = $template->execute(['Калмыкия']);
		}


		$this->assertNotEmpty($result);
		var_dump($result);
	}
}