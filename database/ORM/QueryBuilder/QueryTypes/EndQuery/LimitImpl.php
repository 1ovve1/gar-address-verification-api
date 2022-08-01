<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\EndQuery;

use DB\ORM\QueryBuilder\AbstractSQL\EndQuery;
use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

class LimitImpl implements ActiveRecord, EndQuery
{
use ActiveRecordImpl;

	public function __construct(ActiveRecord $parent, int $count)
	{
		$this->initQueryBox(
			template: SQL::LIMIT,
			clearArgs: [$count],
			parentBox: $parent->getQueryBox()
		);
	}
}