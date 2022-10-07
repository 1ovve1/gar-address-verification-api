<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\OrderBy;

use DB\ORM\DBFacade;

trait OrderByTrait
{
	public function orderBy(string|array $field, bool $asc = true): OrderByQuery
	{
		if (is_array($field)) {
			$field = DBFacade::mappedFieldsToString($field);
		}
		return new ImplOrderBy($this, $field, $asc);
	}
}