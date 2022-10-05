<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder\Where;

use DB\ORM\QueryBuilder\QueryTypes\Where\WhereAble;
use DB\ORM\QueryBuilder\QueryTypes\Where\WhereTrait;
use Tests\Mock\FakeActiveRecordImpl;

class WhereMock extends FakeActiveRecordImpl implements WhereAble
{
	use WhereTrait;
}