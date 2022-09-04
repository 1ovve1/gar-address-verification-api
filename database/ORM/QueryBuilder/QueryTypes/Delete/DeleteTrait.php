<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Delete;

trait DeleteTrait
{
	public static function delete(?string $tableName = null): DeleteQuery
	{
		$tableName ??= self::table();

		return new ImplDelete($tableName);
	}
}