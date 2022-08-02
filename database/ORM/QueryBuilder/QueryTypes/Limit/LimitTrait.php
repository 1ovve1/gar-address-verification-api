<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Limit;

trait LimitTrait
{
	public function limit(int $count): LimitQuery
	{
		return new ImplLimit($this, $count);
	}
}