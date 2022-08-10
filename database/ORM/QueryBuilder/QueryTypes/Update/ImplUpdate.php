<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Update;

use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;
use DB\ORM\QueryBuilder\ActiveRecord\QueryBox;

class ImplUpdate extends UpdateQuery
{
	function __construct(string $field,
	                     float|int|bool|string|null $value,
	                     string $tableName)
	{
		parent::__construct(
			$this::createQueryBox(
				template: SQL::UPDATE,
				clearArgs: [$tableName, $field],
				dryArgs: [$value],
			)
		);
	}
}