<?php declare(strict_types=1);

namespace DB\ORM\Table\QueryTypes\Join;

use DB\ORM\Table\QueryTypes\SelectQueryImpl;
use DB\ORM\Table\SQL\SelectQuery;
use DB\ORM\Table\Templates\SQL;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\QueryBox;

class LeftJoinImpl extends SelectQueryImpl implements SelectQuery, ActiveRecord
{
	function __construct(ActiveRecord $parent,
	                     string $joinTable,
	                     string $leftField,
	                     string $rightField)
	{
		$this->initQueryBox(
			template: SQL::LEFT_JOIN,
			clearArgs: [$joinTable, $leftField, $rightField],
			dryArgs: [], parentBox: $parent->getQueryBox()
		);
	}
}