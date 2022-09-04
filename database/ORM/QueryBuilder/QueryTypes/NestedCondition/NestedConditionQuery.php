<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

abstract class NestedConditionQuery
	extends ActiveRecordImpl
	implements NestedContinueConditionAble
{
use NestedContinueConditionTrait;
}