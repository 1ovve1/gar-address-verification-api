<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Condition;

use DB\ORM\QueryBuilder\QueryTypes\Condition\ConditionAble;
use DB\ORM\QueryBuilder\QueryTypes\Condition\ConditionTrait;
use Tests\Mock\FakeActiveRecordImpl;

class ConditionMock extends FakeActiveRecordImpl implements ConditionAble
{
	use ConditionTrait;
}