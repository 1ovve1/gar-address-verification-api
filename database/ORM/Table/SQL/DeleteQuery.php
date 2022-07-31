<?php

declare(strict_types=1);

namespace DB\ORM\Table\SQL;

/**
 * Delete query interface
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface DeleteQuery
{
	/**
	 * Create WHERE template
	 *
	 * @param  string|callable $field_or_nested_clbk - name of field or callback for nested-or-where [OR (...)]
	 * @param  DatabaseContract|string|null $sign_or_value - sign for compare or value for default '=' compare
	 * @param  DatabaseContract|null $value - value to compare
	 * @return ContinueWhere
	 */
	public function where(string|callable $field_or_nested_clbk,
	                      mixed $sign_or_value = null,
	                      mixed $value = null): ContinueWhere;

    /**
     * Reset query buffer
     * @return QueryModel
     */
    public function reset(): QueryModel;

    /**
     * Save and execute query
     *
     * @return array<mixed>
     */
    public function save(): array;

    /**
     * Create template with name $name
     *
     * @param  string $name - name of template
     * @return void
     */
    public function name(string $name): void;
  
    /**
     * Check if template with name $checkName exists
     * @param  string $checkName - name of template
     * @return bool
     */
    public function nameExist(string $checkName): bool;

    /**
     * Execute template with name $templateName by $values
     * @param  array<DatabaseContract> $values - values to execute
     * @param  string|null $templateName - name of template
     * @return array<mixed>
     */
    public function execute(array $values, ?string $templateName = null): array;
}
