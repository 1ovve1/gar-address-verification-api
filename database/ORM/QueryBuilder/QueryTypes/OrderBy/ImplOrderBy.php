<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\OrderBy;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

class ImplOrderBy extends OrderByQuery
{
	function __construct(ActiveRecord $parent, string $fields, bool $asc)
	{
		parent::__construct(
			$this::createQueryBox(
				clearArgs: [$fields, ($asc) ? 'ASC': 'DESC'],
				parentBox: $parent->getQueryBox()
			)
		);
	}
}