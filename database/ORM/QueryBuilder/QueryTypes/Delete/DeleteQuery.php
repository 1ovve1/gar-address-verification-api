<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Delete;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereAble;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereTrait;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

abstract class DeleteQuery
	extends ActiveRecordImpl
	implements WhereAble
{
use WhereTrait;
}