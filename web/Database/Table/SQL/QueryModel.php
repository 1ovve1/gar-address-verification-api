<?php

declare(strict_types=1);

namespace GAR\Database\Table\SQL;

/**
 * Common interface of query model table
 *
 * @phpstan-import-type DatabaseContract from \GAR\Database\DBAdapter\DBAdapter
 */
interface QueryModel
{
    /**
     * Create insert template
     *
     * @param  array<string, DatabaseContract> $values - values in field => value fmt
     * @param  string|null $tableName - name of table
     * @return EndQuery
     */
    public function insert(array $values, ?string $tableName = null): EndQuery;

    /**
     * Doing forceInsert
     *
     * @param  array<DatabaseContract> $values - values for the force insert
     * @return EndQuery
     */
    public function forceInsert(array $values): EndQuery;

    /**
     * Create update template
     *
     * @param  string $field - field for update
     * @param  DatabaseContract $value - value for upadte
     * @param  string|null $tableName - name of table
     * @return UpdateQuery
     */
    public function update(string $field, mixed $value, ?string $tableName = null): UpdateQuery;

    /**
     * Creating delete template
     *
     * @param  string|null $tableName - name of table
     * @return DeleteQuery
     */
    public function delete(?string $tableName = null): DeleteQuery;

    /**
     * Creating select template
     *
     * @param  array<string> $fields - fields to select
     * @param  array<string>|null $anotherTables - name of another table
     * @return SelectQuery
     */
    public function select(array $fields, ?array $anotherTables = null): SelectQuery;

    /**
     * Finding first element of $field collumn with $value compare
     *
     * @param  string $field - fields name
     * @param  DatabaseContract $value - value for compare
     * @param  string|null $anotherTable - table name
     * @return array<mixed>
     */
    public function findFirst(string $field, mixed $value, ?string $anotherTable = null): array;

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
