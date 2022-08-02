<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Where;

use DB\ORM\QueryBuilder\QueryTypes\ContinueWhere\ContinueWhereAble;
use DB\ORM\QueryBuilder\QueryTypes\ContinueWhere\ContinueWhereTrait;
use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitAble;
use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitTrait;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

abstract class WhereQuery
	extends ActiveRecordImpl
	implements ActiveRecord, ContinueWhereAble, LimitAble
{
use ContinueWhereTrait, LimitTrait;
}