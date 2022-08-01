<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\OrderBy;

trait OrderByTrait
{
	public function orderBy(string $field, bool $asc = true): ImplOrderBy
	{
		return new OrderByImpl($this, $field, $asc);
	}
}