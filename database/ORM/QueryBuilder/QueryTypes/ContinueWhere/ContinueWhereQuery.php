<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;

use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

abstract class ContinueWhereQuery
	extends ActiveRecordImpl
	implements ContinueWhereAble
{
use ContinueWhereTrait;

}