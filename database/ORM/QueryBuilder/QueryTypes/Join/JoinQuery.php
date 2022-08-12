<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Join;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereAble;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereTrait;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

abstract class JoinQuery
	extends ActiveRecordImpl
	implements WhereAble, JoinAble
{
use WhereTrait, JoinTrait;

}