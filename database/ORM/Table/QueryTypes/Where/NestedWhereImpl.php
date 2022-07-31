<?php declare(strict_types=1);

namespace DB\ORM\Table\QueryTypes\Where;

use DB\ORM\Table\SQL\WhereQuery;
use DB\ORM\Table\Templates\SQL;
use DB\ORM\Table\Utils\ActiveRecord;
use DB\ORM\Table\Utils\ActiveRecordImpl;

class NestedWhereImpl extends WhereImpl implements ActiveRecord, WhereQuery
{
use ActiveRecordImpl;

	public function __construct(?ActiveRecord $parent = null, ?callable $callback = null)
	{
		if (null === $parent) {
			$this->initQueryBox(SQL::EMPTY);
		} else if (null !== $callback) {
			$clone = $callback(new $this());
			if ($clone instanceof ActiveRecord) {
				$clearArg = $clone->getQueryBox()->getPreparedQueryFromQueryBox();
				$this->initQueryBox(
					template: SQL::WHERE_NESTED,
					clearArgs: [$clearArg]
				);
			}
		}
	}
}