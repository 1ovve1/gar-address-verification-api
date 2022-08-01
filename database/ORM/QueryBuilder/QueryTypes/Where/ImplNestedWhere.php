<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Where;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereQuery;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
use DB\ORM\QueryBuilder\Utils\QueryBox;
use DB\ORM\QueryBuilder\Utils\SQLNestedWhereConstructor;
use DB\ORM\QueryBuilder\Templates\SQL;

class ImplNestedWhere extends WhereQuery
{
	public function __construct(ActiveRecord $parent, callable $callback)
	{
		$nestedBuilder = new SQLNestedWhereConstructor();
		$callback($nestedBuilder);
		parent::__construct(
			$this->createQueryBox(
				SQL::WHERE_NESTED, [$nestedBuilder->getQuery()],
				$nestedBuilder->getBuffer(), $parent->getQueryBox()
			)
		);
	}
}