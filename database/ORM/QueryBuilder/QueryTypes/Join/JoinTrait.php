<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Join;

use DB\ORM\DBFacade;

trait JoinTrait
{
	/**
	 * @inheritDoc
	 */
	public function innerJoin(string $table, array $condition): JoinQuery
	{
		[$leftSideField, $rightSideField] = $this::joinArgsHandler($table, $condition);

		return new ImplInnerJoin($this, $table, $leftSideField, $rightSideField);
	}

	/**
	 * @inheritDoc
	 */
	public function leftJoin(string $table, array $condition): JoinQuery
	{
		[$leftSideField, $rightSideField] = $this::joinArgsHandler($table, $condition);

		return new ImplLeftJoin($this, $table, $leftSideField, $rightSideField);
	}

	/**
	 * @inheritDoc
	 */
	public function rightJoin(string $table, array $condition): JoinQuery
	{
		[$leftSideField, $rightSideField] = $this::joinArgsHandler($table, $condition);

		return new ImplRightJoin($this, $table, $leftSideField, $rightSideField);
	}

	protected static function joinArgsHandler(string $table, array $condition): array
	{
		return match (count($condition)) {
			1 => [key($condition), current($condition)],
			2 => $condition,
			default => DBFacade::dumpException(
				null,
				'Condition count are incorrect (use [field1 => field2] or [field1, field2] notation]',
				func_get_args()
			)
		};
	}
}