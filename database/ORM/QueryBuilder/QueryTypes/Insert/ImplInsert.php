<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Insert;

use DB\ORM\DBFacade;


class ImplInsert extends InsertQuery
{
	/**
	 * @param array<string> $fields
	 * @param array<DatabaseContract> $values
	 * @param string $tableName
	 */
	public function __construct(array $fields, array $values, string $tableName)
	{
		$fieldsStr = implode(', ', $fields);
		$varsTemplate = DBFacade::genInsertVars(count($fields), count($values) / count($fields));

		parent::__construct(
			$this::createQueryBox(
				clearArgs: [$tableName, $fieldsStr, $varsTemplate],
				dryArgs: $values
			)
		);
	}
}