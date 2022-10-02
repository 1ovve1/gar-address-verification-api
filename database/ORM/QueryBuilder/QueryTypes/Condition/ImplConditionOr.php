<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

class ImplConditionOr extends ContinueConditionQuery
{
	public function __construct(ActiveRecord $parent,
	                            string $field,
	                            string $sign,
	                            float|int|bool|string|null $value)
	{
		parent::__construct(
			$this::createQueryBox(
				clearArgs: [$field, $sign],
				dryArgs: [$value],
				parentBox: $parent->getQueryBox()
			)
		);

	}
}