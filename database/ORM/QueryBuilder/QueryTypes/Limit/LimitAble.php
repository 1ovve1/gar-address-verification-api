<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Limit;

interface LimitAble
{
	/**
	 * Create LIMIT $count template
	 * @param  positive-int $count - limit count
	 * @return LimitQuery
	 */
	public function limit(int $count): LimitQuery;
}