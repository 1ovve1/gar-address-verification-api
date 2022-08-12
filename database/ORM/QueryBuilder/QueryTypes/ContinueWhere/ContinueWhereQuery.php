<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;

use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitAble;
use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitTrait;
use DB\ORM\QueryBuilder\QueryTypes\OrderBy\OrderByAble;
use DB\ORM\QueryBuilder\QueryTypes\OrderBy\OrderByTrait;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

abstract class ContinueWhereQuery
	extends ActiveRecordImpl
	implements ContinueWhereAble, OrderByAble, LimitAble
{
use ContinueWhereTrait, OrderByTrait, LimitTrait;

}