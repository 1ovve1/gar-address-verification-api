<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Delete;

interface DeleteAble
{
	/**
		 * Creating delete template
		 *
		 * @param  string|null $tableName - name of table
		 * @return DeleteQuery
		 */

	public static function delete(?string $tableName = null): DeleteQuery;
}