<?php

namespace DB\ORM\Table\QueryTypes\Where;

use DB\ORM\Table\SQL\WhereQuery;
use DB\ORM\Table\Templates\SQL;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\ActiveRecordImpl;

class WhereAndImpl extends WhereImpl implements ActiveRecord, WhereQuery
{
use ActiveRecordImpl;

	public function __construct(ActiveRecord $parent, string $field, string $sign, float|bool|int|string $value)
	{
		$this->initQueryBox(
			template: SQL::WHERE_AND,
			clearArgs: [$field, $sign],
			dryArgs: [$value],
			parentBox: $parent->getQueryBox()
		);
	}
}