<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\ContinueWhere;

use DB\ORM\QueryBuilder\QueryTypes\ContinueWhere\ContinueWhereAble;
use DB\ORM\QueryBuilder\QueryTypes\ContinueWhere\ContinueWhereTrait;
use Tests\Mock\FakeActiveRecordImpl;

class ContinueWhereMock extends FakeActiveRecordImpl implements ContinueWhereAble
{
	use ContinueWhereTrait;
}