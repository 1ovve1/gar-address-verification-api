<?php

namespace DB\ORM\QueryBuilder\QueryTypes\NestedWhere;

use DB\ORM\QueryBuilder\QueryTypes\ContinueWhere\ContinueWhereAble;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereAble;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereQuery;
use DB\ORM\QueryBuilder\Utils\QueryBox;

abstract class NestedWhere implements WhereAble, ContinueWhereAble
{
	private QueryBox $mutableQueryBox;

	/**
	 * @inheritDoc
	 */
	public function where(callable|string $field_or_nested_clbk, float|bool|int|string $sign_or_value = '', float|bool|int|string|null $value = null): WhereQuery
	{
		// TODO: Implement where() method.
	}


}