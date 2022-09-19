<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Limit;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

class ImplLimit extends LimitQuery
{
	public function __construct(ActiveRecord $parent, int $count)
	{
		parent::__construct(
			$this->createQueryBox(
				clearArgs: [$count],
				parentBox: $parent->getQueryBox()
			)
		);
	}
}