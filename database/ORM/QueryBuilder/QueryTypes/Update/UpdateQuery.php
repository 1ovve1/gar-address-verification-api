<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Update;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereAble;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereTrait;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

abstract class UpdateQuery
	extends ActiveRecordImpl
	implements WhereAble
{
use WhereTrait;

}