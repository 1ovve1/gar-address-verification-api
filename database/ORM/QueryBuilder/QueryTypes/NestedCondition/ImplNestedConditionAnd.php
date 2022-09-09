<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

class ImplNestedConditionAnd extends NestedContinueConditionQuery
{
	/**
	 * @param ActiveRecord $parent
	 * @param string $field
	 * @param string $sign
	 * @param DatabaseContract $value
	 */
	public function __construct(ActiveRecord $parent,
	                            string $field,
	                            string $sign,
	                            int|float|bool|string|null $value)
	{
		parent::__construct(
			$this::createQueryBox(
				template: SQL::WHERE_AND,
				clearArgs: [$field, $sign],
				dryArgs: [$value],
				parentBox: $parent->getQueryBox()
			)
		);
	}
}