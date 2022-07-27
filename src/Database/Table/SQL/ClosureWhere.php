<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;


/**
 * Closure where object
 *
 * @phpstan-import-type DatabaseContract from \GAR\Database\DBAdapter\DBAdapter
 */
interface ClosureWhere
{
	/**
	 * Create WHERE template
	 *
	 * @param  string|callable $field - name of field or callback [(...)]
	 * @param  string $sign - sign for compare
	 * @param  DatabaseContract $value - value to compare
	 * @return ClosureWhere
	 */
	public function where(string|callable $field, string $sign = '', mixed $value = ''): ClosureWhere;

	/**
	 * Create AND WHERE template
	 *
	 * @param  string|callable $field - name of field or callback [AND (...)]
	 * @param  string $sign - sign for compare
	 * @param  DatabaseContract - value to compare
	 * @return ClosureWhere
	 */
	public function andWhere(string|callable $field, string $sign = '', mixed $value = ''): ClosureWhere;

	/**
	 * Create OR WHERE template
	 *
	 * @param  string|callable $field - name of field or callback [OR (...)]
	 * @param  string $sign - sign for compare
	 * @param  DatabaseContract - value to compare
	 * @return ClosureWhere
	 */
	public function orWhere(string|callable $field, string $sign = '', mixed $value = ''): ClosureWhere;
}