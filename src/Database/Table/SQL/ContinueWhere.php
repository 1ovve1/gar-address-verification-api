<?php

namespace GAR\Database\Table\SQL;

/**
 * Continue where interface
 *
 * @phpstan-import-type DatabaseContract from \GAR\Database\DBAdapter\DBAdapter
 */
interface ContinueWhere
{
    /**
     * Create AND WHERE template
     *
     * @param  string $field - name of field
     * @param  string $sign - sign for compare
     * @param  DatabaseContract $value - value to compare
     * @return ContinueWhere
     */
    public function andWhere(string $field, string $sign, mixed $value): ContinueWhere;

    /**
     * Create OR WHERE template
     *
     * @param  string $field - name of field
     * @param  string $sign - sign for compare
     * @param  DatabaseContract $value - value to compare
     * @return ContinueWhere
     */
    public function orWhere(string $field, string $sign, mixed $value): ContinueWhere;

    /**
     * Creating ORDER BY template
     *
     * @param  string $field - field to sort
     * @param  bool $asc - type of sort
     * @return EndQuery
     */
    public function orderBy(string $field, bool $asc = true): EndQuery;

    /**
     * Create LIMIT $count template
     * @param  positive-int $count - limit count
     * @return EndQuery
     */
    public function limit(int $count): EndQuery;

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
