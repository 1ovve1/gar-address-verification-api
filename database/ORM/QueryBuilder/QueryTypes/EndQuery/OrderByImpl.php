<?php

namespace DB\ORM\QueryBuilder\QueryTypes\EndQuery;

use DB\ORM\QueryBuilder\AbstractSQL\EndQuery;
use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

class OrderByImpl implements ActiveRecord, EndQuery
{
use ActiveRecordImpl;

	public function __construct(ActiveRecord $parent, string $field, bool $asc)
	{
		$this->initQueryBox(
			template: ($asc) ? SQL::GROUP_BY_ASK: SQL::GROUP_BY_DESK,
			clearArgs: [$field],
			parentBox: $parent->getQueryBox()
		);
	}
}