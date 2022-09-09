<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Delete;

use DB\ORM\QueryBuilder\QueryBuilder;

trait DeleteTrait
{
	public static function delete(?string $tableName = null): DeleteQuery
	{
		$tableName ??= QueryBuilder::table(static::class);

		return new ImplDelete($tableName);
	}
}