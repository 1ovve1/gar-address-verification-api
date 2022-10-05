<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Limit;

trait LimitTrait
{
	public function limit(int $count): LimitQuery
	{
		if ($count < 0) {
			throw new \RuntimeException("Negative limit values given, use >= 0 ('{$count}')");
		}
		return new ImplLimit($this, $count);
	}
}