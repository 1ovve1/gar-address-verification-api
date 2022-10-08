<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Condition;

use DB\ORM\QueryBuilder\QueryTypes\Condition\ContinueConditionAble;
use DB\ORM\QueryBuilder\QueryTypes\Condition\ContinueConditionTrait;
use Tests\Mock\FakeActiveRecordImpl;

class ContinueConditionMock extends FakeActiveRecordImpl implements ContinueConditionAble
{
	use ContinueConditionTrait;
}