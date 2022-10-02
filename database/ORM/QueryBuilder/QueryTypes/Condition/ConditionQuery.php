<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Condition;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

abstract class ConditionQuery
	extends ActiveRecordImpl
	implements ContinueConditionAble
{
use ContinueConditionTrait;
}