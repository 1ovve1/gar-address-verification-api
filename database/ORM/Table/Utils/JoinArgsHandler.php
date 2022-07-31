<?php declare(strict_types=1);

namespace DB\ORM\Table\Utils;

use DB\ORM\DBFacade;

trait JoinArgsHandler
{
	public function joinArgsHandler(string $table, array $condition): array
	{
		return match (count($condition)) {
			1 => [key($condition), current($condition)],
			2 => $condition,
			default => DBFacade::dumpException(
				$this,
				'Condition count are incorrect (use [field1 => field2] or [field1, field2] notation]',
				func_get_args()
			)
		};
	}
}