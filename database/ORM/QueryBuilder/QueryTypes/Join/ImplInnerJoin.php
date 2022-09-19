<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Join;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

class ImplInnerJoin extends JoinQuery
{
	function __construct(ActiveRecord $parent,
	                     string $joinTable,
	                     string $leftField,
	                     string $rightField)
	{
		parent::__construct(
			$this::createQueryBox(
				clearArgs: [$joinTable, $leftField, $rightField],
				dryArgs: [], parentBox: $parent->getQueryBox()
			)
		);
	}
}