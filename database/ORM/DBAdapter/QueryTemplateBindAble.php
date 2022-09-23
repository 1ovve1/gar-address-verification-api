<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter;

/**
 * Common query template interface for prepared statements
 */
interface QueryTemplateBindAble extends QueryTemplate
{
	/**
	 * Bind parameters by reference for actual prepare statement
	 * @param array<DatabaseContract>|array<string, array<DatabaseContract>> &$params - params reference (buffer, var or another)
	 * @param bool $columnMod - bind params by columns form ({col1: [1, 2, 3], col2: [2, 3, 4]} and etc)
	 * @return $this
	 */
	public function bindParams(array &$params = [], bool $columnMod = false): self;

	/**
	 * Bind values for actual prepare statement
	 * @param array<DatabaseContract> $values
	 * @return $this
	 */
	public function bindValues(array $values = []): self;
}
