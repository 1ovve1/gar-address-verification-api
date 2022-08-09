<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Update;

interface UpdateAble
{
	/**
	 * Create update template
	 *
	 * @param string $field - field for update
	 * @param float|int|bool|string|null $value - value for update
	 * @param string|null $tableName - name of table
	 * @return UpdateQuery
	 */
	public static function update(string $field,
	                              float|int|bool|string|null $value,
	                              ?string $tableName = null): UpdateQuery;
}