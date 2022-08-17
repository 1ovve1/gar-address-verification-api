<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Delete;

use DB\ORM\DBFacade;

trait DeleteTrait
{
	public static function delete(?string $tableName = null): DeleteQuery
	{
		$tableName ??= self::getTableName();

		return new ImplDelete($tableName);
	}
}