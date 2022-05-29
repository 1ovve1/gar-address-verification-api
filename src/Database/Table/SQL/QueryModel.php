<?php declare(strict_types=1);

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
  function insert(array $values, ?string $tableName = null) : EndQuery;

  /**
   * Doing forceInsert
   * 
   * @param  array<DatabaseContract> $values - values for the force insert
   * @return EndQuery
   */
  function forceInsert(array $values) : EndQuery;

  /**
   * Create update template
   * 
   * @param  string $field - field for update
   * @param  DatabaseContract $value - value for upadte
   * @param  string|null $tableName - name of table
   * @return UpdateQuery
   */
  function update(string $field, mixed $value, ?string $tableName = null) : UpdateQuery;

  /**
   * Creating delete template
   * 
   * @param  string|null $tableName - name of table
   * @return DeleteQuery
   */
  function delete(?string $tableName = null) : DeleteQuery;

  /**
   * Creating select template
   * 
   * @param  array<string> $fields - fields to select
   * @param  array<string>|null $anotherTables - name of another table
   * @return SelectQuery
   */
  function select(array $fields, ?array $anotherTables = null) : SelectQuery;

  /**
   * Finding first element of $field collumn with $value compare
   * 
   * @param  string $field - fields name
   * @param  DatabaseContract $value - value for compare
   * @param  string|null $anotherTable - table name
   * @return array<string, mixed>
   */
  function findFirst(string $field, mixed $value, ?string $anotherTable = null): array;

  /**
   * Check if template with name $checkName exists
   * @param  string $checkName - name of template
   * @return bool
   */
  function nameExist(string $checkName) : bool;

  /**
   * Execute template with name $templateName by $values
   * @param  array<DatabaseContract> $values - values to execute
   * @param  string|null $templateName - name of template
   * @return array<string, mixed>
   */
  function execute(array $values, ?string $templateName = null) : array;
}