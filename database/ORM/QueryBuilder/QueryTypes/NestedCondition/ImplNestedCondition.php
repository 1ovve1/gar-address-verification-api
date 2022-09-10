<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\QueryBuilder\Templates\SQL;

class ImplNestedCondition extends NestedConditionQuery
{

	/**
	 * @param string $field
	 * @param string $sign
	 * @param DatabaseContract $value
	 */
	public function __construct(string $field,
                                string $sign,
                                int|float|bool|string|null $value)
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