<?php

declare(strict_types=1);

namespace GAR\Database\Table\SQL;

/**
 * Delete query interface
 *
 * @phpstan-import-type DatabaseContract from \GAR\Database\DBAdapter\DBAdapter
 */
interface DeleteQuery
{
	/**
	 * Create WHERE template
	 *
	 * @param  string $field - name of field
	 * @param  string $sign - sign for compare
	 * @param  DatabaseContract $value - value to compare
	 * @return ContinueWhere
	 */
	public function where(string $field, string $sign, mixed $value): ContinueWhere;

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
