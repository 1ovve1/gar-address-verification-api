<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Delete;

use DB\ORM\QueryBuilder\Templates\SQL;

class ImplDelete extends DeleteQuery
{
	public function __construct(string $tableName)
	{
		parent::__construct(
			$this::createQueryBox(
				template: SQL::DELETE,
				clearArgs: [$tableName]
			)
		);
	}
}