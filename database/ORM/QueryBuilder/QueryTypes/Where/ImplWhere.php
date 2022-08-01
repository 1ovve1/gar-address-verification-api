<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Where;

use DB\ORM\QueryBuilder\QueryTypes\EndQuery\LimitImpl;
use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;

class ImplWhere extends WhereQuery
{
	public function __construct(ActiveRecord $parent,
	                            string $field,
	                            string $sign,
	                            float|int|bool|string $value)
	{
		parent::__construct(
			$this::createQueryBox(
				template: SQL::WHERE,
				clearArgs: [$field, $sign],
				dryArgs: [$value],
				parentBox: $parent->getQueryBox()
			)
		);
	}





	/**
	 * @inheritDoc
	 */
	public function limit(int $count): LimitImpl
	{
		return new LimitImpl($this, $count);
	}
}