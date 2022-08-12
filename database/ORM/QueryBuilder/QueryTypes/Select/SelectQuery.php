<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\ORM\QueryBuilder\QueryTypes\{Join\JoinAble,
	Join\JoinTrait,
	Limit\LimitAble,
	Limit\LimitTrait,
	OrderBy\OrderByAble,
	OrderBy\OrderByTrait,
	Where\WhereAble,
	Where\WhereTrait};
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

abstract class SelectQuery
	extends ActiveRecordImpl
	implements WhereAble, JoinAble, LimitAble, OrderByAble
{
use WhereTrait, JoinTrait, LimitTrait, OrderByTrait;

}