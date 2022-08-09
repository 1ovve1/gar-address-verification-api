<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\OrderBy;


interface OrderByAble
{
	/**
	 * Creating ORDER BY template
	 *
	 * @param string|array<int|string, string|array<int, string>> $field - field to sort
	 * @param bool $asc - type of sort
	 * @return OrderByQuery
	 */
	public function orderBy(string|array $field, bool $asc = true): OrderByQuery;
}