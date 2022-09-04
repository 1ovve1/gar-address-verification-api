<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;

use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

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