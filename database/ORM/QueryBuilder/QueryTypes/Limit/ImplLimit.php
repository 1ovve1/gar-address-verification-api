<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Limit;

use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;

class ImplLimit extends LimitQuery
{
	public function __construct(ActiveRecord $parent, int $count)
	{
		parent::__construct(
			$this->createQueryBox(
				template: SQL::LIMIT,
				clearArgs: [$count],
				parentBox: $parent->getQueryBox()
			)
		);
	}
}