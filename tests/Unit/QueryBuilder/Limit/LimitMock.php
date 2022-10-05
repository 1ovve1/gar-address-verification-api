<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Limit;

use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitAble;
use DB\ORM\QueryBuilder\QueryTypes\Limit\LimitTrait;
use Tests\Mock\FakeActiveRecordImpl;

class LimitMock extends FakeActiveRecordImpl implements LimitAble
{
	use LimitTrait;
}