<?php declare(strict_types=1);

namespace DB\ORM\Table\QueryTypes\EndQuery;

use DB\ORM\Table\SQL\EndQuery;
use DB\ORM\Table\Templates\SQL;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\ActiveRecordImpl;

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