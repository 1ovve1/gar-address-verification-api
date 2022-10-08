<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Join;

use DB\ORM\QueryBuilder\QueryTypes\Join\JoinAble;
use DB\ORM\QueryBuilder\QueryTypes\Join\JoinTrait;
use Tests\Mock\FakeActiveRecordImpl;

class JoinMock extends FakeActiveRecordImpl implements JoinAble
{
	use JoinTrait;
}