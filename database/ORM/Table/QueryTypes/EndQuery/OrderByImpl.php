<?php

namespace DB\ORM\Table\QueryTypes\EndQuery;

use DB\ORM\Table\SQL\EndQuery;
use DB\ORM\Table\Templates\SQL;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\ActiveRecordImpl;

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