<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\ORM\QueryBuilder\QueryTypes\Join\JoinAble;
use DB\ORM\QueryBuilder\QueryTypes\Join\JoinTrait;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereAble;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereTrait;
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

abstract class SelectQuery
	extends ActiveRecordImpl
	implements WhereAble, JoinAble
{
use WhereTrait, JoinTrait;

}