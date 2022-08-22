<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;

class ImplInsert extends InsertQuery
{
	/**
	 * @param String[] $fields
	 * @param array<array<mixed>> $values
	 * @param string $tableName
	 */
	public function __construct(array $fields, array $values, string $tableName)
	{
		$fieldsStr = implode(', ', $fields);
		$varsTemplate = DBFacade::genInsertVars(count($fields), count($values) / count($fields));

		parent::__construct(
			$this::createQueryBox(
				template: SQL::INSERT, clearArgs: [$tableName, $fieldsStr, $varsTemplate],
				dryArgs: $values
			)
		);
	}
}