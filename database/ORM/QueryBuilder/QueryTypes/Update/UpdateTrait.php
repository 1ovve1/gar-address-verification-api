<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Update;

use DB\ORM\DBFacade;

trait UpdateTrait
{
	public static function update(string $field,
	                              float|int|bool|string|null $value,
	                              ?string $tableName = null): UpdateQuery
	{
		$tableName ??= self::table();
		return new ImplUpdate($field, $value, $tableName);
	}
}