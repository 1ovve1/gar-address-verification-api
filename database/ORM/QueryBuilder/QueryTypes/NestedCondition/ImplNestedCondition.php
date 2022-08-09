<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\QueryBuilder\Templates\SQL;

class ImplNestedCondition extends NestedConditionQuery
{

	public function __construct(string $field,
                                string $sign,
	                            float|int|bool|string|null $value)
	{
		parent::__construct(
			$this::createQueryBox(
				template: SQL::CONDITION,
				clearArgs: [$field, $sign],
				dryArgs: [$value],
			)
		);
	}
}