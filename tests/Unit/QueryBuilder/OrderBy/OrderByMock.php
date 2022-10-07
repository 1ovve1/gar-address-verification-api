<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\OrderBy;

use DB\ORM\QueryBuilder\QueryTypes\OrderBy\OrderByAble;
use DB\ORM\QueryBuilder\QueryTypes\OrderBy\OrderByTrait;
use Tests\Mock\FakeActiveRecordImpl;

class OrderByMock extends FakeActiveRecordImpl implements OrderByAble
{
	use OrderByTrait;
}