<?php

declare(strict_types=1);

namespace DB\ORM\Table\SQL;

/**
 * Select query interface
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
interface SelectQuery
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
     * Create INNER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function innerJoin(string $table, array $condition): SelectQuery;

    /**
     * Create LEFT OUTER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function leftJoin(string $table, array $condition): SelectQuery;

    /**
     * Create RIGHT OUTER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function rightJoin(string $table, array $condition): SelectQuery;

    /**
     * Create LIMIT $count template
     * @param  positive-int $count - limit count
     * @return EndQuery
     */
    public function limit(int $count): EndQuery;

    /**
     * Creating ORDER BY template
     *
     * @param  string $field - name of field
     * @param  bool|boolean $asc - type of sort
     * @return EndQuery
     */
    public function orderBy(string $field, bool $asc = true): EndQuery;

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
