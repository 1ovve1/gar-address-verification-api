<?php

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;

use DB\ORM\QueryBuilder\AbstractSQL\WhereQuery;
use DB\ORM\QueryBuilder\QueryTypes\Where\ImplWhere;
use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

class ImplWhereOr extends ContinueWhereQuery
{
	public function __construct(ActiveRecord $parent,
	                            string $field,
	                            string $sign,
	                            float|int|bool|string|null $value)
	{
		parent::__construct(
			$this::createQueryBox(
				template: SQL::WHERE_OR,
				clearArgs: [$field, $sign],
				dryArgs: [$value],
				parentBox: $parent->getQueryBox()
			)
		);

	}
}