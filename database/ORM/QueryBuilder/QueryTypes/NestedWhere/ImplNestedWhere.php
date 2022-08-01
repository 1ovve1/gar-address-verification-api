<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedWhere;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereQuery;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;
use DB\ORM\QueryBuilder\Utils\QueryBox;

class ImplNestedWhere extends WhereQuery
{
	public function __construct(ActiveRecord )
	{
		parent::__construct($queryBox);
	}
}