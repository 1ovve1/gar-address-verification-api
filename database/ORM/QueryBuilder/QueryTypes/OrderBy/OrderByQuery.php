<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\OrderBy;

use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitAble;
use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitTrait;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

abstract class OrderByQuery
	extends ActiveRecordImpl
	implements LimitAble
{
use LimitTrait;

}