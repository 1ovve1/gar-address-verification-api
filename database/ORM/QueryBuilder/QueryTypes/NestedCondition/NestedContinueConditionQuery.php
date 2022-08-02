<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

class NestedContinueConditionQuery
	extends ActiveRecordImpl
	implements NestedContinueConditionAble
{
use NestedContinueConditionTrait;
}