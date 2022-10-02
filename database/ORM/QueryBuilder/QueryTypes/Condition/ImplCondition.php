<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;


class ImplCondition extends ConditionQuery
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
				clearArgs: [$field, $sign],
				dryArgs: [$value],
			)
		);
	}
}